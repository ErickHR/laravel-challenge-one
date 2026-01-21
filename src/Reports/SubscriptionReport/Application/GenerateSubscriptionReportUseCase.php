<?php

namespace Src\Reports\SubscriptionReport\Application;

use App\Enums\DebtType;
use App\Enums\EntityType;
use App\Enums\StatusProcessReport;
use Illuminate\Support\Facades\Log;
use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportCreditCardsRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportLoandRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportOtherDebtRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\SubscriptionReportRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\SubscriptionRepository;
use Src\Shared\GenerateXLSX;

class GenerateSubscriptionReportUseCase
{
    private $registerDownloadRepository;
    private $subscriptionReportRepository;
    private $subscriptionRepository;
    private $reportLoandRepository;
    private $reportOtherDebtRepository;
    private $reportCreditCardsRepository;

    public function __construct(
        RegisterDownloadRepository $registerDownloadRepository,
        SubscriptionReportRepository $subscriptionReportRepository,
        SubscriptionRepository $subscriptionRepository,
        ReportLoandRepository $reportLoandRepository,
        ReportOtherDebtRepository $reportOtherDebtRepository,
        ReportCreditCardsRepository $reportCreditCardsRepository
    ) {
        $this->registerDownloadRepository = $registerDownloadRepository;
        $this->subscriptionReportRepository = $subscriptionReportRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->reportLoandRepository = $reportLoandRepository;
        $this->reportOtherDebtRepository = $reportOtherDebtRepository;
        $this->reportCreditCardsRepository = $reportCreditCardsRepository;
    }

    public function execute($filters, $fileName)
    {

        $start = microtime(true);

        $from = $filters['from'];
        $to = $filters['to'];

        Log::info($fileName);

        $registerDownload = $this->registerDownloadRepository->getByFileName($fileName);

        Log::info(json_encode($this->registerDownloadRepository));
        Log::info(json_encode($registerDownload));

        if ($registerDownload) {
            $this->registerDownloadRepository->updateStatus($registerDownload->getId(), StatusProcessReport::PROCESSING->value);
        }

        $this->generateReport(
            $fileName,
            [
                'from' => $from,
                'to' => $to
            ]
        );

        if ($registerDownload) {
            $this->registerDownloadRepository->updateStatus($registerDownload->getId(), StatusProcessReport::COMPLETED->value);
        }

        $endTotal = microtime(true);
        Log::info('V3 - Tiempo TOTAL del Job: ' . round($endTotal - $start, 4) . ' segundos');
    }

    public function generateReport($fileName, $filters)
    {
        try {
            Log::error('Filename: ' . $fileName);

            $filePath = storage_path('app/public/' . $fileName);

            $documentXLSX = new GenerateXLSX();
            $documentXLSX->init();
            $documentXLSX->openToFile($filePath);
            $documentXLSX->writeHeaders();

            foreach ($this->getData($filters) as $row) {
                $documentXLSX->addRow($row);
            }

            $documentXLSX->close();
        } catch (\Exception $e) {
            Log::error('Error al generar el archivo XLSX: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getData($filters)
    {
        $lastId = 0;

        while (true) {
            $subscriptionReports = $this->subscriptionReportRepository->getByLastId($lastId, $filters);

            // Verificar si hay resultados usando isEmpty() para Collections
            if ($subscriptionReports->isEmpty()) break;

            foreach ($this->processChunk($subscriptionReports, $filters) as $row) {
                yield $row;
            }

            // Usar last() de Collection en lugar de end() de array
            $lastId = $subscriptionReports->last()->id;

            unset($subscriptionReports);
        }
    }

    private function processChunk($subscriptionReports, array $filters): \Generator
    {
        $subscriptionReportsIds = $subscriptionReports->pluck('id')->toArray();
        $subscriptionIds = $subscriptionReports->pluck('subscription_id')->toArray();

        $subscriptions = $this->subscriptionRepository->byIds($subscriptionIds);
        $reportLoans = $this->reportLoandRepository->getBySubscriptionReportId($subscriptionReportsIds, $filters);
        $reportOtherDebts = $this->reportOtherDebtRepository->getBySubscriptionReportId($subscriptionReportsIds, $filters);
        $reportCreditCards = $this->reportCreditCardsRepository->getBySubscriptionReportId($subscriptionReportsIds, $filters);


        foreach ($subscriptionReports as $subscriptionReport) {

            $subscription = $subscriptions->get($subscriptionReport->subscription_id);

            foreach ($this->buildReportLoans($subscriptionReport, $subscription, $reportLoans) as $row) {
                yield $row;
            }
            foreach ($this->buildReportOtherDebts($subscriptionReport, $subscription, $reportOtherDebts) as $row) {
                yield $row;
            }
            foreach ($this->buildReportCreditCards($subscriptionReport, $subscription, $reportCreditCards) as $row) {
                yield $row;
            }
        }

        unset($subscriptions, $loans, $otherDebts, $creditCards);
    }

    private function buildReportLoans($subscriptionReport, $subscription, $reportLoans): \Generator
    {

        foreach ($reportLoans->get($subscriptionReport->id, []) as $reportLoan) {
            $status = $this->calculateStatus($reportLoan->status, $reportLoan->expiration_days);

            yield [
                $subscriptionReport->id,
                $subscription->full_name,
                $subscription->document,
                $subscription->email,
                $subscription->phone,
                $reportLoan->bank,
                DebtType::LOAN->label(),
                $reportLoan->status,
                $reportLoan->expiration_days,
                EntityType::FINANCIAL->label(),
                $reportLoan->amount . ' ' . $reportLoan->currency,
                '',
                '',
                $subscriptionReport->created_at,
                $status,
            ];
        }
    }

    private function buildReportOtherDebts($subscriptionReport, $subscription, $reportOtherDebts): \Generator
    {
        foreach ($reportOtherDebts->get($subscriptionReport->id, []) as $reportOtherDebt) {
            $status = ($reportOtherDebt->expiration_days == 0) ? 'Al día' : 'Con atraso';

            yield [
                $subscriptionReport->id,
                $subscription->full_name,
                $subscription->document,
                $subscription->email,
                $subscription->phone,
                $reportOtherDebt->entity,
                DebtType::OTHER_DEBT->label(),
                '',
                $reportOtherDebt->expiration_days,
                EntityType::COMMERCIAL->label(),
                $reportOtherDebt->amount . ' ' . $reportOtherDebt->currency,
                '',
                '',
                $subscriptionReport->created_at,
                $status,
            ];
        }
    }

    private function buildReportCreditCards($subscriptionReport, $subscription, $reportCreditCards): \Generator
    {

        foreach ($reportCreditCards->get($subscriptionReport->id, []) as $reportCreditCard) {
            yield [
                $subscriptionReport->id,
                $subscription->full_name,
                $subscription->document,
                $subscription->email,
                $subscription->phone,
                $reportCreditCard->bank,
                DebtType::CREDIT_CARD->label(),
                '',
                '',
                EntityType::FINANCIAL->label(),
                '',
                $reportCreditCard->line,
                $reportCreditCard->used,
                $subscriptionReport->created_at,
                '',
            ];
        }
    }

    private function calculateStatus(string $loanStatus, int $expirationDays): string
    {
        return ($loanStatus === 'NOR' && $expirationDays == 0)
            ? 'Al día'
            : 'Con atraso';
    }
}
