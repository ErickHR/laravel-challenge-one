<?php

namespace Src\Reports\SubscriptionReport\Application\UseCase;

use Src\Reports\SubscriptionReport\Application\Dto\ReportRowDto;
use Src\Reports\SubscriptionReport\Application\Service\ReportDataProcessor;
use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportCreditCardsRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportLoandRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportOtherDebtRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\SubscriptionReportRepository;
use Src\Reports\SubscriptionReport\Domain\Repository\SubscriptionRepository;
use Src\Reports\SubscriptionReport\Domain\Service\Report\FileReportWriter;

use function PHPSTORM_META\type;

class GenerateSubscriptionReportUseCase
{

    public function __construct(
        private RegisterDownloadRepository $registerDownloadRepository,
        private SubscriptionReportRepository $subscriptionReportRepository,
        private SubscriptionRepository $subscriptionRepository,
        private ReportLoandRepository $reportLoandRepository,
        private ReportOtherDebtRepository $reportOtherDebtRepository,
        private ReportCreditCardsRepository $reportCreditCardsRepository,
        private ReportDataProcessor $reportDataProcessor,
        private FileReportWriter $reportBuilder,
    ) {}

    public function execute($filters, $fileName)
    {
        $registerDownload = $this->registerDownloadRepository->getByFileName($fileName);

        if ($registerDownload) {
            $registerDownload->markAsProcessing();
            $this->registerDownloadRepository->updateStatus($registerDownload);
        }

        $this->generateReport($fileName, $filters);

        if ($registerDownload) {
            $registerDownload->markAsCompleted();
            $this->registerDownloadRepository->updateStatus($registerDownload);
        }

        $endTotal = microtime(true);
    }

    public function generateReport($fileName, $filters)
    {
        try {
            $filePath = storage_path('app/public/' . $fileName);

            $this->reportBuilder->initialize($filePath);
            $this->reportBuilder->addHeaders(ReportRowDto::headers());

            foreach ($this->getData($filters) as $row) {
                $this->reportBuilder->appendRow($row);
            }

            $this->reportBuilder->close();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getData($filters)
    {
        $lastId = 0;

        while (true) {
            $subscriptionReports = $this->subscriptionReportRepository->getByLastId($lastId, $filters);

            if ($subscriptionReports->isEmpty()) break;

            foreach ($this->processChunk($subscriptionReports, $filters) as $row) {
                yield $row;
            }

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


        foreach (
            $this->reportDataProcessor->processChunk(
                $subscriptionReports,
                $subscriptions,
                $reportLoans,
                $reportOtherDebts,
                $reportCreditCards
            ) as $row
        ) {
            yield $row;
        }

        unset($subscriptions, $loans, $otherDebts, $creditCards);
    }
}
