<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Controllers;

use Src\Reports\SubscriptionReport\Application\Service\RegisterDownloadImpl;
use Src\Reports\SubscriptionReport\Application\UseCase\DownloadSubscriptionReportUseCase;
use Src\Reports\SubscriptionReport\Application\UseCase\SaveSubscriptionReportUseCase;
use Src\Reports\SubscriptionReport\Domain\Exception\ReportNotFoundException;
use Src\Reports\SubscriptionReport\Infrastructure\Dto\GetRegisterDownloadRequest;
use Src\Reports\SubscriptionReport\Infrastructure\Dto\StoreSubscriptionReportRequest;
use Src\Reports\SubscriptionReport\Infrastructure\Service\Storage\DocumentManagerService;

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
            $documentManagerService = new DocumentManagerService();

            $downloadSubscriptionReportUseCase = new DownloadSubscriptionReportUseCase($registerDownloadImpl, $documentManagerService);
            $result = $downloadSubscriptionReportUseCase->execute($request);
            return  response()->download(
                $result['file_path'],
                $result['file_name'],
            );
        } catch (ReportNotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al descargar el reporte',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
