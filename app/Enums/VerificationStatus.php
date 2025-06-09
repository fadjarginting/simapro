<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case BELUM = 'Belum Verifikasi';
    case FINISH = 'Finish Verifikasi';
    case IN_PROGRESS = 'In Progress Verifikasi';

    public function label(): string
    {
        return match($this) {
            self::BELUM => 'Belum Verifikasi',
            self::FINISH => 'Finish Verifikasi',
            self::IN_PROGRESS => 'In Progress Verifikasi',
        };
    }
}
