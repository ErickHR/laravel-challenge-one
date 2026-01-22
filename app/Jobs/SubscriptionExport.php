<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Support\Facades\Log;

use Src\Reports\SubscriptionReport\Application\Service\RegisterDownloadImpl;
use Src\Reports\SubscriptionReport\Application\Service\ReportDataProcessor;
use Src\Reports\SubscriptionReport\Application\UseCase\FailedGenerateSubscriptionReportUseCase;
use Src\Reports\SubscriptionReport\Application\UseCase\GenerateSubscriptionReportUseCase;
use Src\Reports\SubscriptionReport\Infrastructure\Mapper\ReportCreditCardsMapper;
use Src\Reports\SubscriptionReport\Infrastructure\Mapper\ReportLoanMapper;
use Src\Reports\SubscriptionReport\Infrastructure\Mapper\ReportOtherDebtsMapper;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\ReportCreditCardImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\ReportLoanImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\ReportOtherDebtImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\SubscriptionImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\SubscriptionReportImpl;
use Src\Reports\SubscriptionReport\Infrastructure\Service\Xlsx\XlsxReportWriter;

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

        $reportDataProcessor = new ReportDataProcessor(
            new ReportCreditCardsMapper(),
            new ReportLoanMapper(),
            new ReportOtherDebtsMapper()
        );

        $generateSubscriptionReportUseCase = new GenerateSubscriptionReportUseCase(
            app(RegisterDownloadImpl::class),
            app(SubscriptionReportImpl::class),
            app(SubscriptionImpl::class),
            app(ReportLoanImpl::class),
            app(ReportOtherDebtImpl::class),
            app(ReportCreditCardImpl::class),

            $reportDataProcessor,
            app(XlsxReportWriter::class),

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
