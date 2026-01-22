<?php

namespace Src\Reports\SubscriptionReport\Infrastructure\Mapper;

use Src\Reports\SubscriptionReport\Application\Dto\ReportRowDto;
use Src\Reports\SubscriptionReport\Application\Mapper\ReportRowMapper;

use Src\Reports\SubscriptionReport\Domain\ValueObject\DebtType;
use Src\Reports\SubscriptionReport\Domain\ValueObject\EntityType;

class ReportLoanMapper implements ReportRowMapper
{
    public function map(
        object $subscriptionReport,
        object $subscription,
        object $loan
    ): \Generator {
        $status = $this->calculateStatus($loan->status ?? '', $loan->expiration_days ?? 0);

        yield new ReportRowDto(
            subscriptionReportId: $subscriptionReport->id,
            fullName: $subscription->full_name ?? '',
            document: $subscription->document ?? '',
            email: $subscription->email ?? '',
            phone: $subscription->phone ?? '',
            company: $loan->bank ?? '',
            debtType: DebtType::LOAN->label(),
            loanStatus: $loan->status ?? '',
            expirationDays: (string)($loan->expiration_days ?? ''),
            entityType: EntityType::FINANCIAL->label(),
            totalAmount: ($loan->amount ?? '') . ' ' . ($loan->currency ?? ''),
            totalLine: '',
            usedLine: '',
            reportDate: $subscriptionReport->created_at ?? '',
            status: $status
        );
    }

    public function mapCollection(
        object $subscriptionReport,
        object $subscription,
        iterable $loans
    ): \Generator {
        foreach ($loans as $loan) {
            yield from $this->map($subscriptionReport, $subscription, $loan);
        }
    }

    private function calculateStatus(?string $loanStatus, ?int $expirationDays): string
    {
        if ($loanStatus === null || $expirationDays === null) {
            return 'Desconocido';
        }

        return ($loanStatus === 'NOR' && $expirationDays == 0)
            ? 'Al día'
            : 'Con atraso';
    }
}
