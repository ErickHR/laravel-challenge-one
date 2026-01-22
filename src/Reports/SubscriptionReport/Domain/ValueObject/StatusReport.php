<?php

namespace Src\Reports\SubscriptionReport\Domain\ValueObject;

enum StatusReport: string
{
    case NOR = 'NOR';
    case CPP = 'CPP';
    case DEF = 'DEF';
    case PER = 'PER';

    public function label(): string
    {
        return match ($this) {
            self::NOR => 'Normal',
            self::CPP => 'Con problemas potenciales',
            self::DEF => 'Deficiencia',
            self::PER => 'Pérdida',
        };
    }
}
