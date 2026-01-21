<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Controllers;

use Src\Reports\SubscriptionReport\Infrastructure\DTO\GetRegisterDownloadRequest;
use Src\Reports\SubscriptionReport\Infrastructure\DTO\StoreSubscriptionReportRequest;
use Src\Reports\SubscriptionReport\Infrastructure\Repository\RegisterDownloadImpl;

use Src\Reports\SubscriptionReport\Application\DownloadSubscriptionReportUseCase;
use Src\Reports\SubscriptionReport\Application\SaveSubscriptionReportUseCase;

class SubscriptionReportController
{
    public function save(StoreSubscriptionReportRequest $request)
    {
        try {
            $registerDownloadImpl = new RegisterDownloadImpl();
            $saveSubscriptionReportUseCase = new SaveSubscriptionReportUseCase($registerDownloadImpl);

            $dataReport = $saveSubscriptionReportUseCase->execute($request);
            return response()->json($dataReport);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function download(GetRegisterDownloadRequest $request)
    {
        try {
            $registerDownloadImpl = new RegisterDownloadImpl();
            $downloadSubscriptionReportUseCase = new DownloadSubscriptionReportUseCase($registerDownloadImpl);

            return  $downloadSubscriptionReportUseCase->execute($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al descargar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
