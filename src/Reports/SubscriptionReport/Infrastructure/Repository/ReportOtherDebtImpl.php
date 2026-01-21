<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;
use Src\Reports\SubscriptionReport\Domain\Repository\ReportOtherDebtRepository;

class ReportOtherDebtImpl implements ReportOtherDebtRepository
{
    public function getBySubscriptionReportId($subscriptionReportsIds, $filters)
    {
        return DB::table('report_other_debts')
            ->select('subscription_report_id', 'entity', 'expiration_days', 'amount', 'currency')
            ->whereBetween('created_at', [$filters['from'], $filters['to']])
            ->whereIn('subscription_report_id', $subscriptionReportsIds)
            ->get()
            ->groupBy('subscription_report_id');
    }
}
