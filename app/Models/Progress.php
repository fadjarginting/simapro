<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progresses'; // <-- pastikan ini ditambahkan

    protected $fillable = [
        'title',
        'plant_id', // Ubah dari plant menjadi plant_id
        'work_priority',
        'job_type',
        'request_category',
        'no_erf',
        'erf_approved_date',
        'erf_clarification_date',
        'erf_validated_date',
        'lead_engineering',
        'pic_mekanikal',
        'progress_mekanikal',
        'pic_sipil',
        'progress_sipil',
        'pic_elinst',
        'progress_elinst',
        'pic_proses',
        'progress_proses',
        'uk_peminta',
        'status_verifikasi',
        'deadline_initiating',
        'deadline_executing',
        'status',
        'fase',
        'progress_description',
        'note',
        'entry_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'progress_mekanikal' => 'decimal:2',
        'progress_sipil' => 'decimal:2',
        'progress_elinst' => 'decimal:2',
        'progress_proses' => 'decimal:2',
        'erf_approved_date' => 'date',
        'erf_clarification_date' => 'date',
        'erf_validated_date' => 'date',
        'deadline_initiating' => 'date',
        'deadline_executing' => 'date',
        'entry_date' => 'date',
    ];

    /**
     * Calculate the average progress across all disciplines
     *
     * @return float
     */
    public function calculateTotalProgress()
    {
        $progressFields = [
            $this->progress_mekanikal,
            $this->progress_sipil,
            $this->progress_elinst,
            $this->progress_proses
        ];

        // Filter out null values
        $validFields = array_filter($progressFields, function ($value) {
            return $value !== null;
        });

        if (empty($validFields)) {
            return 0;
        }

        return round(array_sum($validFields) / count($validFields), 2);
    }
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
