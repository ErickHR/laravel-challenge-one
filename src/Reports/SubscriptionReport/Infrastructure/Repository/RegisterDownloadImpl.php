<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Repository;

use App\Models\RegisterDownloand;
use Illuminate\Support\Facades\Log;
use Src\Reports\SubscriptionReport\Domain\Entity\RegisterDownload;

use Src\Reports\SubscriptionReport\Domain\Repository\RegisterDownloadRepository;

class RegisterDownloadImpl implements RegisterDownloadRepository
{

    public function getByFileName(string $fileName): ?RegisterDownload
    {
        $record = RegisterDownloand::where('file_name', $fileName)->first();

        if ($record) {
            $registerDownload = new RegisterDownload();
            $registerDownload->setId($record->id);
            $registerDownload->setFileName($record->file_name);
            $registerDownload->setFilePath($record->file_path);
            $registerDownload->setStatus($record->status);
            return $registerDownload;
        }

        return null;
    }

    public function save(RegisterDownload $registerDownload): void
    {
        RegisterDownloand::create([
            'file_name' => $registerDownload->getFileName(),
            'file_path' => $registerDownload->getFilePath(),
            'status' => $registerDownload->getStatus(),
        ]);
    }

    public function updateStatus(int $id, bool $status): void
    {
        RegisterDownloand::where('id', $id)->update(['status' => $status]);
    }
}
