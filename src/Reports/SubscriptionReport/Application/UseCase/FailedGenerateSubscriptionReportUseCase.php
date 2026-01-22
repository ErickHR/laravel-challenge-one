<?php

namespace Src\Reports\SubscriptionReport\Application\UseCase;

use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;

class FailedGenerateSubscriptionReportUseCase
{
    protected $registerDownloadRepository;

    public function __construct(RegisterDownloadRepository $registerDownloadRepository)
    {
        $this->registerDownloadRepository = $registerDownloadRepository;
    }

    public function execute($fileName)
    {
        $registerDownload = $this->registerDownloadRepository->getByFileName($fileName);
        $registerDownload->markAsFailed();

        if ($registerDownload) {
            $this->registerDownloadRepository->updateStatus($registerDownload);
        }
    }
}
