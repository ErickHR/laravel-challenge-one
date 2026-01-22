<?php

namespace Src\Reports\SubscriptionReport\Domain\Repository;

use Src\Reports\SubscriptionReport\Domain\Entity\RegisterDownload;

interface RegisterDownloadRepository
{
    public function getByFileName(string $fileName): ?RegisterDownload;
    public function save(RegisterDownload $registerDownload): void;
    public function updateStatus(RegisterDownload $registerDownload): void;
}
