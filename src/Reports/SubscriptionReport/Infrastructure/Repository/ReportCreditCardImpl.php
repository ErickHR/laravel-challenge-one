<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Repository;

use Illuminate\Support\Facades\DB;

use Src\Reports\SubscriptionReport\Domain\Repository\ReportCreditCardsRepository;

class ReportCreditCardImpl implements ReportCreditCardsRepository
{
    public function getBySubscriptionReportId($subscriptionReportsIds, $filters)
    {
        return DB::table('report_credit_cards')
            ->select('subscription_report_id', 'bank', 'currency', 'line', 'used')
            ->whereBetween('created_at', [$filters['from'], $filters['to']])
            ->whereIn('subscription_report_id', $subscriptionReportsIds)
            ->get()
            ->groupBy('subscription_report_id');
    }
}
