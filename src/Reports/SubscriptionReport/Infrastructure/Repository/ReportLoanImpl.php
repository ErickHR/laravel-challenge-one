<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;

use Src\Reports\SubscriptionReport\Domain\Repository\ReportLoandRepository;

class ReportLoanImpl implements ReportLoandRepository
{
    public function getBySubscriptionReportId($subscriptionReportsIds, $filters)
    {
        return DB::table('report_loans')
            ->select('subscription_report_id', 'bank', 'status', 'expiration_days', 'amount', 'currency')
            ->whereBetween('created_at', [$filters['from'], $filters['to']])
            ->whereIn('subscription_report_id', $subscriptionReportsIds)
            ->get()
            ->groupBy('subscription_report_id');
    }
}
