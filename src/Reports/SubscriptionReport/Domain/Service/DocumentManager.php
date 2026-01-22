<?php

namespace Src\Reports\SubscriptionReport\Domain\Service;

interface DocumentManager
{
    public function exists(string $fileName): bool;
    public function generateReport(string $fileName, array $filters): void;
}
