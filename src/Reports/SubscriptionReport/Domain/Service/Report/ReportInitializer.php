<?php

namespace Src\Reports\SubscriptionReport\Domain\Service\Report;

interface ReportInitializer
{
    public function initialize(string $filePath): void;
}
