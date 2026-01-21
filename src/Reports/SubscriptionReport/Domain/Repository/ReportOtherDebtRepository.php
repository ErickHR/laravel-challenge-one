<?php

namespace Src\Reports\SubscriptionReport\Domain\Repository;

interface ReportOtherDebtRepository
{
    public function getBySubscriptionReportId($subscriptionReportsIds, $filters);
}
