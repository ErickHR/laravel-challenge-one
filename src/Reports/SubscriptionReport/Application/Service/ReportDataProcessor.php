<?php

namespace Src\Reports\SubscriptionReport\Application\Service;

use Src\Reports\SubscriptionReport\Application\Mapper\ReportRowMapper;

class ReportDataProcessor
{
    public function __construct(
        private readonly ReportRowMapper $reportLoanMapper,
        private readonly ReportRowMapper $reportOtherDebtsMapper,
        private readonly ReportRowMapper $reportCreditCardsMapper
    ) {}

    public function processChunk(
        $subscriptionReports,
        $subscriptions,
        $reportLoans,
        $reportOtherDebts,
        $reportCreditCards
    ): \Generator {
        foreach ($subscriptionReports as $subscriptionReport) {
            $subscription = $subscriptions->get($subscriptionReport->subscription_id);

            if (!$subscription) {
                continue;
            }

            foreach ($this->reportLoanMapper->mapCollection($subscriptionReport, $subscription, $reportLoans->get($subscriptionReport->id, [])) as $row) {
                yield $row;
            }

            foreach ($this->reportOtherDebtsMapper->mapCollection($subscriptionReport, $subscription, $reportOtherDebts->get($subscriptionReport->id, [])) as $row) {
                yield $row;
            }

            foreach ($this->reportCreditCardsMapper->mapCollection($subscriptionReport, $subscription, $reportCreditCards->get($subscriptionReport->id, [])) as $row) {
                yield $row;
            }
        }
    }
}
