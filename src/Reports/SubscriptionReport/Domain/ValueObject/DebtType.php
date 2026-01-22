<?php

namespace Src\Reports\SubscriptionReport\Domain\ValueObject;

enum DebtType: string
{
    case LOAN = 'loan';
    case CREDIT_CARD = 'credit_card';
    case OTHER_DEBT = 'other_debt';

    public function label(): string
    {
        return match ($this) {
            self::LOAN => 'Préstamo',
            self::CREDIT_CARD => 'Tarjeta de crédito',
            self::OTHER_DEBT => 'Otra deuda',
        };
    }
}
