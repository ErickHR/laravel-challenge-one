<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Mapper;

use Src\Reports\SubscriptionReport\Application\Dto\ReportRowDto;
use Src\Reports\SubscriptionReport\Application\Mapper\ReportRowMapper;

use Src\Reports\SubscriptionReport\Domain\ValueObject\DebtType;
use Src\Reports\SubscriptionReport\Domain\ValueObject\EntityType;

class ReportCreditCardsMapper implements ReportRowMapper
{
    public function map(
        object $subscriptionReport,
        object $subscription,
        object $reportCreditCard
    ): \Generator {

        yield  new ReportRowDto(
            subscriptionReportId: $subscriptionReport->id,
            fullName: $subscription->full_name ?? '',
            document: $subscription->document ?? '',
            email: $subscription->email ?? '',
            phone: $subscription->phone ?? '',
            company: $reportCreditCard->bank ?? '',
            debtType: DebtType::CREDIT_CARD->label(),
            loanStatus: '',
            expirationDays: '',
            entityType: EntityType::FINANCIAL->label(),
            totalAmount: '',
            totalLine: (string)($reportCreditCard->line ?? ''),
            usedLine: (string)($reportCreditCard->used ?? ''),
            reportDate: $subscriptionReport->created_at ?? '',
            status: ''
        );
    }

    public function mapCollection(
        object $subscriptionReport,
        object $subscription,
        iterable $reportCreditCards
    ): \Generator {
        foreach ($reportCreditCards as $reportCreditCard) {
            yield from $this->map($subscriptionReport, $subscription, $reportCreditCard);
        }
    }
}
