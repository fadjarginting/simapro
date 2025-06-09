<?php

namespace App\Enums;

enum WorkPriority: string
{
    case HIGH = 'HIGH';
    case MEDIUM = 'MEDIUM';

    public function label(): string
    {
        return match($this) {
            self::HIGH => 'High',
            self::MEDIUM => 'Medium',
        };
    }
}
