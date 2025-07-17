<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case BELUM = 'Belum Verifikasi';
    case IN_PROGRESS = 'In Progress Verifikasi';
    case FINISH = 'Finish Verifikasi';

    public function label(): string
    {
        return match($this) {
            self::BELUM => 'Belum Verifikasi',
            self::IN_PROGRESS => 'In Progress Verifikasi',
            self::FINISH => 'Finish Verifikasi',
        };
    }
}
