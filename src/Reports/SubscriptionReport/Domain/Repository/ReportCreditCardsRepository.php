<?php

namespace Src\Reports\SubscriptionReport\Domain\Repository;

interface ReportCreditCardsRepository
{
    public function getBySubscriptionReportId($subscriptionReportsIds, $filters);
}
