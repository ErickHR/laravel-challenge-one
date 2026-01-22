<?php

namespace Src\Reports\SubscriptionReport\Application\UseCase;

use Src\Reports\SubscriptionReport\Domain\Entity\RegisterDownload;
use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;
use Src\Reports\SubscriptionReport\Domain\Exception\ReportNotFoundException;
use Src\Reports\SubscriptionReport\Domain\Service\DocumentManager;

class DownloadSubscriptionReportUseCase
{

    private $registerDownloadRepository;
    private $documentManagerService;

    public function __construct(
        RegisterDownloadRepository $registerDownloadRepository,
        DocumentManager $documentManagerService
    ) {
        $this->registerDownloadRepository = $registerDownloadRepository;
        $this->documentManagerService = $documentManagerService;
    }

    public function execute($request)
    {
        $registerDownload = new RegisterDownload();
        $registerDownload->setFileName($request['fileName']);

        $registerDownload = $this->registerDownloadRepository->getByFileName($registerDownload->getFileName());
        if (!$registerDownload) {
            throw new ReportNotFoundException('El archivo aún no está disponible. Por favor, inténtelo más tarde.');
        }

        if (!$this->documentManagerService->exists($registerDownload->getFileName())) {
            throw new ReportNotFoundException('El archivo no se pudo generar');
        }

        return [
            'file_path' => $registerDownload->getFilePath(),
            'file_name' => $registerDownload->getFileName()
        ];
    }
}
