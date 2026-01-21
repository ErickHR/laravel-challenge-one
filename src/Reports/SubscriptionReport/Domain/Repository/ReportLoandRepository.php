<?php

namespace Src\Reports\SubscriptionReport\Domain\Repository;

interface ReportLoandRepository
{
    public function getBySubscriptionReportId($subscriptionReportsIds, $filters);
}
