<?php

namespace App\Enums;

enum CurrentPhase: string
{
    case NOT_STARTED = 'Not started';
    case INITIATING = 'Initiating';
    case PLANNING = 'Planning';
    case EXECUTING = 'Executing';
    case CLOSING = 'Closing';
    case HOLD = 'Hold';
    case CANCEL = 'Cancel';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not started',
            self::INITIATING => 'Initiating',
            self::PLANNING => 'Planning',
            self::EXECUTING => 'Executing',
            self::CLOSING => 'Closing',
            self::HOLD => 'Hold',
            self::CANCEL => 'Cancel',
        };
    }
}
