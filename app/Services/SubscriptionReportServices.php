<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Enums\DebtType;
use App\Enums\EntityType;
use App\Enums\StatusProcessReport;
use App\Enums\StatusReport;
use App\Models\RegisterDownloand;
use App\Models\SubscriptionReport as ModelsSubscriptionReport;

use App\Exports\SubscriptionExportV1;
use App\Jobs\SubscriptionExportV2 as JobsSubscriptionExportV2;
use App\Jobs\SubscriptionExportV3 as JobsSubscriptionExportV3;
use Illuminate\Support\Facades\DB;

class SubscriptionReportServices
{

    const CHUNK_SIZE = 1000;

    public function generateReportV1($filters)
    {
        $start = microtime(true);

        $from = $filters['from'];
        $to = $filters['to'];

        $subscriptionReports = ModelsSubscriptionReport::with([
            'subscription' => function ($query) {
                $query->select('id', 'full_name', 'document', 'email', 'phone', 'created_at');
            },
            'reportLoans' => function ($query) {
                $query->select('subscription_report_id', 'bank', 'status', 'expiration_days', 'amount', 'currency');
            },
            'reportOtherDebts' => function ($query) {
                $query->select('subscription_report_id', 'entity', 'expiration_days', 'amount', 'currency');
            },
            'reportCreditCards' => function ($query) {
                $query->select('subscription_report_id', 'bank', 'currency', 'line', 'used');
            }
        ])
            ->select('id', 'subscription_id', 'created_at')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->get();

        $endQuery = microtime(true);
        Log::info('V1 - Tiempo de consulta BD: ' . round($endQuery - $start, 4) . ' segundos');

        $dataReport = $this->processReportData($subscriptionReports);

        $endProcess = microtime(true);
        Log::info('V1 - Tiempo de procesamiento: ' . round($endProcess - $endQuery, 4) . ' segundos');

        $fileName = 'subscription_report_' . date('Y-m-d_H-i-s') . '.xlsx';
        $response = Excel::download(new SubscriptionExportV1($dataReport), $fileName);

        $endTotal = microtime(true);
        Log::info('V1 - Tiempo de generación Excel: ' . round($endTotal - $endProcess, 4) . ' segundos');
        Log::info('V1 - Tiempo TOTAL del reporte: ' . round($endTotal - $start, 4) . ' segundos');

        return $response;
    }

    public function generateReportV2($filters)
    {
        try {
            $fileName = 'subscription_report_' . date('Y-m-d_H-i-s') . '.xlsx';
            $from = $filters['from'];
            $to = $filters['to'];


            JobsSubscriptionExportV2::dispatch([
                "nameFile" => $fileName,
                "filters" => [
                    "from" => $from,
                    "to" => $to
                ]
            ]);

            RegisterDownloand::create([
                'file_name' => $fileName,
                'file_path' => storage_path('app/public/' . $fileName),
                'status' => StatusProcessReport::PENDING->value,
            ]);

            return [
                'message' => 'Excel Processing started. You can download it later using the provided URL.',
                'file' => $fileName,
                'url' => 'http://challenge.erick-rivas.com/download-stored-excel?filename=' . $fileName
            ];
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte en segundo plano: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateSubscriptionReportCSV($filters)
    {

        $fileName = 'subscription_report_' . date('Y-m-d_H-i-s') . '.csv';
        $filePath = storage_path('app/public/' . $fileName);
        $from = $filters['from'];
        $to = $filters['to'];

        $handle = fopen($filePath, 'w');
        fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
        fputcsv($handle, [
            'ID',
            'Nombre Completo',
            'DNI',
            'Email',
            'Teléfono',
            'Compañía',
            'Tipo de deuda',
            'Situación',
            'Atraso',
            'Entidad',
            'Monto total',
            'Línea total',
            'Línea usada',
            'Reporte subido el',
            'Estado',
        ]);

        $lastId = 0;

        while (true) {
            $subscriptionReports = DB::table('subscription_reports')
                ->select('id', 'subscription_id', 'created_at')
                ->whereBetween('created_at', [$from, $to])
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit(self::CHUNK_SIZE)
                ->get();

            if ($subscriptionReports->isEmpty()) break;

            $lastId = $subscriptionReports->last()->id;

            $subscriptionReportsIds = $subscriptionReports->pluck('id')->toArray();
            $subscriptionIds = $subscriptionReports->pluck('subscription_id')->toArray();

            $subscriptions = DB::table('subscriptions')
                ->whereIn('id', $subscriptionIds)
                ->get()
                ->keyBy('id');

            $reportLoans = DB::table('report_loans')
                ->select('subscription_report_id', 'bank', 'status', 'expiration_days', 'amount', 'currency')
                ->whereIn('subscription_report_id', $subscriptionReportsIds)
                ->get()
                ->groupBy('subscription_report_id');

            Log::info('Mostrenado la query para sacar que indices usar en report other debts');
            Log::info(DB::table('report_loans')
                ->select('subscription_report_id', 'bank', 'status', 'expiration_days', 'amount', 'currency')
                ->whereIn('subscription_report_id', $subscriptionReportsIds)
                ->toSql());

            $reportOtherDebts = DB::table('report_other_debts')
                ->select('subscription_report_id', 'entity', 'expiration_days', 'amount', 'currency')
                ->whereIn('subscription_report_id', $subscriptionReportsIds)
                ->get()
                ->groupBy('subscription_report_id');

            $reportCreditCards = DB::table('report_credit_cards')
                ->select('subscription_report_id', 'bank', 'currency', 'line', 'used')
                ->whereIn('subscription_report_id', $subscriptionReportsIds)
                ->get()
                ->groupBy('subscription_report_id');

            foreach ($subscriptionReports as $subscriptionReportValue) {
                $subscription = $subscriptions->get($subscriptionReportValue->subscription_id);

                foreach ($reportLoans->get($subscriptionReportValue->id, []) as $reportLoanValue) {
                    $status = 'Con atraso';
                    if ($reportLoanValue->status == StatusReport::NOR->value && $reportLoanValue->expiration_days == 0) {
                        $status = 'Al día';
                    }
                    $reportLoansDataExcel = [
                        'id' => $subscriptionReportValue->id,
                        'full_name' => $subscription->full_name,
                        'document' => $subscription->document,
                        'email' => $subscription->email,
                        'phone' => $subscription->phone,
                        'company' => $reportLoanValue->bank,
                        'debt_type' => DebtType::LOAN->label(),
                        'loan_status' => $reportLoanValue->status,
                        'expiration_days' => $reportLoanValue->expiration_days,
                        'entity' => EntityType::FINANCIAL->label(),
                        'amount' => $reportLoanValue->amount,
                        'currency' => $reportLoanValue->currency,
                        'amount_with_currency' => $reportLoanValue->amount . ' ' . $reportLoanValue->currency,
                        'line_total' => '',
                        'line_used' => '',
                        'created_at' => $subscriptionReportValue->created_at,
                        'status' => $status,
                    ];

                    fputcsv($handle, $reportLoansDataExcel);
                    unset($reportLoansDataExcel);
                }

                foreach ($reportOtherDebts->get($subscriptionReportValue->id, []) as $reportOtherDebtValue) {
                    $status = 'Con atraso';
                    if ($reportOtherDebtValue->expiration_days == 0) {
                        $status = 'Al día';
                    }
                    $reportOtherDebtsDataExcel = [
                        'id' => $subscriptionReportValue->id,
                        'full_name' => $subscription->full_name,
                        'document' => $subscription->document,
                        'email' => $subscription->email,
                        'phone' => $subscription->phone,
                        'company' => $reportOtherDebtValue->entity,
                        'debt_type' => DebtType::OTHER_DEBT->label(),
                        'loan_status' => '',
                        'expiration_days' => $reportOtherDebtValue->expiration_days,
                        'entity' => EntityType::COMMERCIAL->label(),
                        'amount' => $reportOtherDebtValue->amount,
                        'currency' => $reportOtherDebtValue->currency,
                        'amount_with_currency' => $reportOtherDebtValue->amount . ' ' . $reportOtherDebtValue->currency,
                        'line_total' => '',
                        'line_used' => '',
                        'created_at' => $subscriptionReportValue->created_at,
                        'status' => $status,
                    ];

                    fputcsv($handle, $reportOtherDebtsDataExcel);
                    unset($reportOtherDebtsDataExcel);
                }

                foreach ($reportCreditCards->get($subscriptionReportValue->id, []) as $reportCreditCardValue) {
                    $reportCreditCardsDataExcel = [
                        'id' => $subscriptionReportValue->id,
                        'full_name' => $subscription->full_name,
                        'document' => $subscription->document,
                        'email' => $subscription->email,
                        'phone' => $subscription->phone,
                        'company' => $reportCreditCardValue->bank,
                        'debt_type' => DebtType::CREDIT_CARD->label(),
                        'loan_status' => '',
                        'expiration_days' => '',
                        'entity' => EntityType::FINANCIAL->label(),
                        'amount' => '',
                        'currency' => $reportCreditCardValue->currency,
                        'amount_with_currency' => '',
                        'line_total' => $reportCreditCardValue->line,
                        'line_used' => $reportCreditCardValue->used,
                        'created_at' => $subscriptionReportValue->created_at,
                        'status' => '',
                    ];
                    fputcsv($handle, $reportCreditCardsDataExcel);
                    unset($reportCreditCardsDataExcel);
                }
            }

            unset(
                $subscriptionReports,
                $subscriptionReportsIds,
                $subscriptionIds,
                $subscriptions,
                $reportLoans,
                $reportOtherDebts,
                $reportCreditCards
            );
        }

        fclose($handle);
    }

    public function generateReportV3($filters)
    {
        try {
            $fileName = 'subscription_report_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = storage_path('app/public/' . $fileName);
            $from = $filters['from'];
            $to = $filters['to'];

            JobsSubscriptionExportV3::dispatch([
                "nameFile" => $fileName,
                "filters" => [
                    "from" => $from,
                    "to" => $to
                ]
            ]);

            RegisterDownloand::create([
                'file_name' => $fileName,
                'file_path' => storage_path('app/public/' . $fileName),
                'status' => StatusProcessReport::PENDING->value,
            ]);

            return [
                'message' => 'Excel Processing started. You can download it later using the provided URL.',
                'file' => $fileName,
                'url' => 'http://challenge.erick-rivas.com/download-stored-excel?filename=' . $fileName
            ];
        } catch (\Exception $e) {
            Log::error('Error al generar el reporte V3: ' . $e->getMessage());
            throw $e;
        }
    }

    public function downloadStoredExcel($filename)
    {
        $registerDownloand = RegisterDownloand::where('file_name', $filename)
            ->where('status', StatusProcessReport::COMPLETED->value)
            ->first();

        if (!$registerDownloand) {
            return response()->json(['message' => 'El archivo aún no está disponible. Por favor, inténtelo más tarde.'], 404);
        }

        if (!Storage::disk('public')->exists($filename)) {
            return response()->json(['message' => 'El archivo no se pudo generar'], 404);
        }

        $filePath = Storage::disk('public')->path($filename);

        return response()->download($filePath, $filename);
    }

    private function processReportData($subscriptionReports)
    {
        $dataReport = [];

        foreach ($subscriptionReports as $subscriptionReportValue) {

            foreach ($subscriptionReportValue->reportLoans as $reportLoanValue) {
                $data = [
                    'id' => $subscriptionReportValue->id,
                    'full_name' => $subscriptionReportValue->subscription->full_name,
                    'document' => $subscriptionReportValue->subscription->document,
                    'email' => $subscriptionReportValue->subscription->email,
                    'phone' => $subscriptionReportValue->subscription->phone,
                    'company' => $reportLoanValue->bank,
                    'debt_type' => DebtType::LOAN->label(),
                    'loan_status' => $reportLoanValue->status,
                    'expiration_days' => $reportLoanValue->expiration_days,
                    'entity' => EntityType::FINANCIAL->label(),
                    'amount' => $reportLoanValue->amount,
                    'currency' => $reportLoanValue->currency,
                    'amount_with_currency' => $reportLoanValue->amount . ' ' . $reportLoanValue->currency,
                    'line_total' => '',
                    'line_used' => '',
                    'created_at' => $subscriptionReportValue->created_at,
                    'status' => '',
                ];
                array_push($dataReport, $data);
            }

            foreach ($subscriptionReportValue->reportOtherDebts as $reportOtherDebtValue) {
                $data = [
                    'id' => $subscriptionReportValue->id,
                    'full_name' => $subscriptionReportValue->subscription->full_name,
                    'document' => $subscriptionReportValue->subscription->document,
                    'email' => $subscriptionReportValue->subscription->email,
                    'phone' => $subscriptionReportValue->subscription->phone,
                    'company' => $reportOtherDebtValue->entity,
                    'debt_type' => DebtType::OTHER_DEBT->label(),
                    'loan_status' => '',
                    'expiration_days' => $reportOtherDebtValue->expiration_days,
                    'entity' => EntityType::COMMERCIAL->label(),
                    'amount' => $reportOtherDebtValue->amount,
                    'currency' => $reportOtherDebtValue->currency,
                    'amount_with_currency' => $reportOtherDebtValue->amount . ' ' . $reportOtherDebtValue->currency,
                    'line_total' => '',
                    'line_used' => '',
                    'created_at' => $subscriptionReportValue->created_at,
                    'status' => '',
                ];
                array_push($dataReport, $data);
            }

            foreach ($subscriptionReportValue->reportCreditCards as $reportCreditCardValue) {
                $data = [
                    'id' => $subscriptionReportValue->id,
                    'full_name' => $subscriptionReportValue->subscription->full_name,
                    'document' => $subscriptionReportValue->subscription->document,
                    'email' => $subscriptionReportValue->subscription->email,
                    'phone' => $subscriptionReportValue->subscription->phone,
                    'company' => $reportCreditCardValue->bank,
                    'debt_type' => DebtType::CREDIT_CARD->label(),
                    'loan_status' => '',
                    'expiration_days' => '',
                    'entity' => EntityType::FINANCIAL->label(),
                    'amount' => '',
                    'currency' => $reportCreditCardValue->currency,
                    'amount_with_currency' => '',
                    'line_total' => $reportCreditCardValue->line,
                    'line_used' => $reportCreditCardValue->used,
                    'created_at' => $subscriptionReportValue->created_at,
                    'status' => '',
                ];
                array_push($dataReport, $data);
            }
        }

        return $dataReport;
    }

    public function generateReportDataLazy($filters, $chunkSize = 500)
    {
        $from = $filters['from'];
        $to = $filters['to'];

        $subscriptionReports = ModelsSubscriptionReport::query()
            ->with([
                'subscription:id,full_name,document,email,phone',
                'reportLoans:subscription_report_id,bank,status,expiration_days,amount,currency',
                'reportOtherDebts:subscription_report_id,entity,expiration_days,amount,currency',
                'reportCreditCards:subscription_report_id,bank,currency,line,used'
            ])
            ->select('id', 'subscription_id', 'created_at')
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('id')
            ->lazy($chunkSize);

        foreach ($subscriptionReports as $subscriptionReportValue) {
            foreach ($this->processSubscriptionReport($subscriptionReportValue) as $row) {
                yield $row;
            }
        }
    }

    private function processSubscriptionReport($subscriptionReportValue)
    {
        $baseData = [
            'id' => $subscriptionReportValue->id,
            'full_name' => $subscriptionReportValue->subscription->full_name,
            'document' => $subscriptionReportValue->subscription->document,
            'email' => $subscriptionReportValue->subscription->email,
            'phone' => $subscriptionReportValue->subscription->phone,
            'created_at' => $subscriptionReportValue->created_at,
        ];

        foreach ($subscriptionReportValue->reportLoans as $loan) {
            yield array_merge($baseData, [
                'company' => $loan->bank,
                'debt_type' => DebtType::LOAN->label(),
                'loan_status' => $loan->status,
                'expiration_days' => $loan->expiration_days,
                'entity' => EntityType::FINANCIAL->label(),
                'amount' => $loan->amount,
                'currency' => $loan->currency,
                'amount_with_currency' => $loan->amount . ' ' . $loan->currency,
                'line_total' => '',
                'line_used' => '',
                'status' => '',
            ]);
        }

        foreach ($subscriptionReportValue->reportOtherDebts as $debt) {
            yield array_merge($baseData, [
                'company' => $debt->entity,
                'debt_type' => DebtType::OTHER_DEBT->label(),
                'loan_status' => '',
                'expiration_days' => $debt->expiration_days,
                'entity' => EntityType::COMMERCIAL->label(),
                'amount' => $debt->amount,
                'currency' => $debt->currency,
                'amount_with_currency' => $debt->amount . ' ' . $debt->currency,
                'line_total' => '',
                'line_used' => '',
                'status' => '',
            ]);
        }

        foreach ($subscriptionReportValue->reportCreditCards as $card) {
            yield array_merge($baseData, [
                'company' => $card->bank,
                'debt_type' => DebtType::CREDIT_CARD->label(),
                'loan_status' => '',
                'expiration_days' => '',
                'entity' => EntityType::FINANCIAL->label(),
                'amount' => '',
                'currency' => $card->currency,
                'amount_with_currency' => '',
                'line_total' => $card->line,
                'line_used' => $card->used,
                'status' => '',
            ]);
        }
    }
}
