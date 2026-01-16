<?php

namespace App\Jobs;

use App\Enums\StatusProcessReport;
use App\Exports\SubscriptionExportV3 as ExportsSubscriptionExportV3;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use OpenSpout\Writer\XLSX\Writer;

use App\Models\RegisterDownloand;
use App\Services\SubscriptionReportServices;
use Illuminate\Support\Facades\Log;

class SubscriptionExportV3 implements ShouldQueue
{
    use Queueable;

    private $data;
    public $timeout = 600; // 10 minutos
    public $tries = 3; // Reintentos en caso de fallo

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     * Esta versión actualiza el estado en la BD automáticamente
     */
    public function handle(): void
    {
        $start = microtime(true);

        $from = $this->data['filters']['from'];
        $to = $this->data['filters']['to'];

        $register = RegisterDownloand::where('file_name', $this->data['nameFile'])->first();
        if ($register) {
            $register->update(['status' => StatusProcessReport::PROCESSING->value]);
        }

        $subscriptionReportServices = new SubscriptionReportServices();
        $subscriptionReportServices->generateSubscriptionReportCSV(
            [
                'from' => $from,
                'to' => $to
            ]
        );

        if ($register) {
            $register->update(['status' => StatusProcessReport::COMPLETED->value]);
        }

        $endTotal = microtime(true);
        Log::info('V3 - Tiempo TOTAL del Job: ' . round($endTotal - $start, 4) . ' segundos');
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $fileName = $this->data['nameFile'];
        $register = RegisterDownloand::where('file_name', $fileName)->first();

        if ($register) {
            $register->update([
                'status' => StatusProcessReport::FAILED->value,
                'error_message' => $exception->getMessage()
            ]);
        }

        Log::error("=== V3: JOB FAILED DEFINITIVAMENTE: " . $exception->getMessage() . " ===");
    }
}
