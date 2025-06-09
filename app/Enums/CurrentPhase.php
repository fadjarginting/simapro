<?php

namespace App\Enums;

enum CurrentPhase: string
{
    case NOT_STARTED = 'Not started';
    case INITIATING = 'Initiating';
    case EXECUTING = 'Executing';
    case CLOSING = 'Closing';
    case HOLD = 'Hold';
    case REJECT = 'Reject';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not started',
            self::INITIATING => 'Initiating',
            self::EXECUTING => 'Executing',
            self::CLOSING => 'Closing',
            self::HOLD => 'Hold',
            self::REJECT => 'Reject',
        };
    }
}
