<?php

namespace App\Enums;

enum RequestCategory: string
{
    case CAPEX = 'CAPEX';
    case OPEX = 'OPEX';

    public function label(): string
    {
        return match($this) {
            self::CAPEX => 'CAPEX',
            self::OPEX => 'OPEX',
        };
    }
}
