<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Support\Facades\Log;

use Src\Reports\SubscriptionReport\Application\FailedGenerateSubscriptionReportUseCase;
use Src\Reports\SubscriptionReport\Application\GenerateSubscriptionReportUseCase;

use Src\Reports\SubscriptionReport\Infrastructure\Repository\RegisterDownloadImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\ReportCreditCardImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\ReportLoanImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\ReportOtherDebtImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\SubscriptionImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\SubscriptionReportImpl;

class SubscriptionExport implements ShouldQueue
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

        $registerDownload = new RegisterDownloadImpl();

        $generateSubscriptionReportUseCase = new GenerateSubscriptionReportUseCase(
            $registerDownload,
            app(SubscriptionReportImpl::class),
            app(SubscriptionImpl::class),
            app(ReportLoanImpl::class),
            app(ReportOtherDebtImpl::class),
            app(ReportCreditCardImpl::class)
        );

        $generateSubscriptionReportUseCase->execute(
            [
                'from' => $this->data['filters']['from'],
                'to' => $this->data['filters']['to']
            ],
            $this->data['fileName'],
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $failedGenerateSubscriptionReportUseCase = new FailedGenerateSubscriptionReportUseCase(
            app(RegisterDownloadImpl::class)
        );
        $failedGenerateSubscriptionReportUseCase->execute($this->data['fileName']);

        Log::error("=== V3: JOB FAILED DEFINITIVAMENTE: " . $exception->getMessage() . " ===");
    }
}
