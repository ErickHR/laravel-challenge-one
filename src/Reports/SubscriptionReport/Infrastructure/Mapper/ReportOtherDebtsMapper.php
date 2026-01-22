<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Mapper;

use Src\Reports\SubscriptionReport\Application\Dto\ReportRowDto;
use Src\Reports\SubscriptionReport\Application\Mapper\ReportRowMapper;
use Src\Reports\SubscriptionReport\Domain\ValueObject\DebtType;
use Src\Reports\SubscriptionReport\Domain\ValueObject\EntityType;

class ReportOtherDebtsMapper implements ReportRowMapper
{
    public function map(
        object $subscriptionReport,
        object $subscription,
        object $reportOtherDebt
    ): \Generator {

        $status = (($reportOtherDebt->expiration_days ?? '') == 0) ? 'Al día' : 'Con atraso';
        yield new ReportRowDto(
            subscriptionReportId: $subscriptionReport->id,
            fullName: $subscription->full_name ?? '',
            document: $subscription->document ?? '',
            email: $subscription->email ?? '',
            phone: $subscription->phone ?? '',
            company: $reportOtherDebt->entity ?? '',
            debtType: DebtType::OTHER_DEBT->label(),
            loanStatus: '',
            expirationDays: $reportOtherDebt->expiration_days ?? '',
            entityType: EntityType::COMMERCIAL->label(),
            totalAmount: $reportOtherDebt->amount ?? '' . ' ' . $reportOtherDebt->currency ?? '',
            totalLine: '',
            usedLine: '',
            reportDate: $subscriptionReport->created_at ?? '',
            status: $status
        );
    }

    public function mapCollection(
        object $subscriptionReport,
        object $subscription,
        iterable $reportOtherDebts
    ): \Generator {
        foreach ($reportOtherDebts as $reportOtherDebt) {
            yield from $this->map($subscriptionReport, $subscription, $reportOtherDebt);
        }
    }
}
