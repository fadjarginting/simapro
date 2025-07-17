<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case NOT_STARTED = 'Not Started';
    case IN_PROGRESS = 'In Progress';
    case FINISH = 'Finish';
    case ON_HOLD = 'On Hold';
    case CANCEL = 'Cancel';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not Started',
            self::IN_PROGRESS => 'In Progress',
            self::FINISH => 'Finish',
            self::ON_HOLD => 'On Hold',
            self::CANCEL => 'Cancel',
        };
    }
}
