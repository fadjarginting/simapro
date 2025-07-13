<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Eat extends Model
{
    use HasFactory;

    protected $table = 'eat';

    protected $fillable = [
        'work_id',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relationships
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->orderBy('discipline_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(EatApproval::class);
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class, 'work_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors & Mutators
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('Y-m-d') : null;
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('Y-m-d') : null;
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByWork($query, $workId)
    {
        return $query->where('work_id', $workId);
    }

    // Helper methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function calculateTotalDuration()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }
        return 0;
    }

    // Boot method to auto-calculate duration
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($eat) {
    //         if ($eat->start_date && $eat->end_date) {
    //             $eat->total_duration_days = $eat->calculateTotalDuration();
    //         }
    //     });
    // }
}