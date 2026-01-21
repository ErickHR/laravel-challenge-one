<?php

namespace Src\Reports\SubscriptionReport\Application;

use Illuminate\Support\Facades\Storage;

use Src\Reports\SubscriptionReport\Domain\Entity\RegisterDownload;
use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;

class DownloadSubscriptionReportUseCase
{

    protected $registerDownloadRepository;

    public function __construct(RegisterDownloadRepository $registerDownloadRepository)
    {
        $this->registerDownloadRepository = $registerDownloadRepository;
    }

    public function execute($request)
    {

        $registerDownload = new RegisterDownload();
        $registerDownload->setFileName($request['fileName']);

        $registerDownload = $this->registerDownloadRepository->getByFileName($registerDownload->getFileName());
        if (!$registerDownload) {
            return response()->json(['message' => 'El archivo aún no está disponible. Por favor, inténtelo más tarde.'], 404);
        }

        if (!Storage::disk('public')->exists($registerDownload->getFileName())) {
            return response()->json(['message' => 'El archivo no se pudo generar'], 404);
        }

        $filePath = Storage::disk('public')->path($registerDownload->getFileName());
        return response()->download($filePath, $registerDownload->getFileName());
    }
}
