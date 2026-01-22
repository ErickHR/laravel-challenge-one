<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Service\Storage;

use Illuminate\Support\Facades\Storage;
use Src\Reports\SubscriptionReport\Domain\Service\DocumentManager;

class DocumentManagerService implements DocumentManager
{
    public function exists(string $fileName): bool
    {
        return Storage::disk('public')->exists($fileName);
    }

    public function generateReport(string $fileName, array $filters): void {}
}
