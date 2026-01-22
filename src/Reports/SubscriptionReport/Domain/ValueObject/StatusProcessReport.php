<?php

namespace Src\Reports\SubscriptionReport\Domain\ValueObject;

enum StatusProcessReport: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PROCESSING => 'En proceso',
            self::COMPLETED => 'Completado',
            self::FAILED => 'Fallido',
        };
    }
}
