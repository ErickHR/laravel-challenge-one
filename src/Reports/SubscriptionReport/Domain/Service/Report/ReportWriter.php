<?php

namespace Src\Reports\SubscriptionReport\Domain\Service\Report;

interface ReportWriter
{
    public function addHeaders(array $headers): void;
    public function appendRow($row): void;
}
