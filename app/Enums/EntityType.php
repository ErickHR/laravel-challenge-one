<?php

namespace App\Enums;

enum EntityType: string
{
    case FINANCIAL = 'financial';
    case COMMERCIAL = 'commercial';

    // Método para obtener el nombre legible (Label)
    public function label(): string
    {
        return match ($this) {
            self::FINANCIAL => 'Entidad financiera',
            self::COMMERCIAL => 'Entidad comercial',
        };
    }
}
