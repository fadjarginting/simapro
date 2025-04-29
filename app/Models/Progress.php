<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progresses'; // <-- pastikan ini ditambahkan

    protected $fillable = [
        'request_category',
        'status_verifikasi',
        'pic_mekanikal',
        'pic_sipil',
        'pic_elinst',
        'pic_proses',
        'progress_mekanikal',
        'progress_sipil',
        'progress_elinst',
        'progress_proses',
        'detail_progress',
        'note',
    ];
}
