<?php

namespace App\Enums;

enum WorkType: string
{
    case FEED_DED = 'FEED/DED';
    case KAJIAN_ENGINEERING = 'Kajian Engineering';
    case TECHNICAL_ASSIST = 'Technical Assist';

    public function label(): string
    {
        return match($this) {
            self::FEED_DED => 'FEED/DED',
            self::KAJIAN_ENGINEERING => 'Kajian Engineering',
            self::TECHNICAL_ASSIST => 'Technical Assist',
        };
    }
}
