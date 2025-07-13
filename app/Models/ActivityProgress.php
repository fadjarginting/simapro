<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'progress_description',
        'progress_percentage',
        'reported_by',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
    ];

    // Relationships
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Scopes
    public function scopeByActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function getFormattedProgressDateAttribute()
    {
        return $this->progress_date ? $this->progress_date->format('d M Y') : null;
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_url);
    }

    // addProgress method to handle progress updates
    public function addProgress(array $data): self
    {
        $this->fill($data);
        $this->save();
        return $this;
    }
    
    // Method to get the latest progress for an activity
    public static function latestProgressForActivity($activityId)
    {
        return self::where('activity_id', $activityId)
            ->latest()
            ->first();
    }
}