<?php

namespace Src\Reports\SubscriptionReport\Application;

use Carbon\Carbon;

use App\Jobs\SubscriptionExport as JobsSubscriptionExport;

use App\Enums\StatusProcessReport;

use Src\Reports\SubscriptionReport\Domain\Entity\RegisterDownload;

use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;

class SaveSubscriptionReportUseCase
{
    protected $registerDownloadRepository;

    public function __construct(RegisterDownloadRepository $registerDownloadRepository)
    {
        $this->registerDownloadRepository = $registerDownloadRepository;
    }

    public function execute($request)
    {

        $filters = [
            'from' => Carbon::parse($request['from'])->startOfDay()->toDateTimeString(),
            'to' => Carbon::parse($request['to'])->endOfDay()->toDateTimeString(),
        ];

        $fileName = 'subscription_report_' . date('Y-m-d_H-i-s') . '.xlsx';

        $registerDownload = new RegisterDownload();
        $registerDownload->setFileName($fileName);
        $registerDownload->setFilePath(storage_path('app/public/' . $fileName));
        $registerDownload->setStatus(StatusProcessReport::PENDING->value);
        $this->registerDownloadRepository->save($registerDownload);

        JobsSubscriptionExport::dispatch([
            "fileName" => $fileName,
            "filters" => [
                "from" => $filters['from'],
                "to" => $filters['to']
            ]
        ]);

        return [
            'message' => 'XLSX Processing started. You can download it later using the provided URL.',
            'file' => $fileName,
            'url' => 'http://challenge.erick-rivas.com/v1/report/download-report?fileName=' . $fileName
        ];
    }
}
