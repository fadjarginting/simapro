<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'eat_id',
        'discipline_id',
        'activity_name',
        'activity_description',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function eat(): BelongsTo
    {
        return $this->belongsTo(Eat::class);
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_pics', 'activity_id', 'user_id')
                    ->withTimestamps();
    }

    // Alias untuk activity_pics relationship
    public function activityPics(): HasMany
    {
        return $this->hasMany(ActivityPic::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ActivityProgress::class)->orderBy('created_at', 'desc');
    }

    // Accessors & Mutators
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('Y-m-d') : null;
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('Y-m-d') : null;
    }

    // Scopes
    public function scopeByDiscipline($query, $disciplineId)
    {
        return $query->where('discipline_id', $disciplineId);
    }

    // Helper methods
    public function calculateDuration()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInDays($this->end_time) + 1;
        }
        return 0;
    }

    public function getPicNamesAttribute()
    {
        return $this->pics->pluck('name')->implode(', ');
    }

    public function getLatestProgressAttribute()
    {
        return $this->progress()->latest('created_at')->first();
    }
    public function latestProgress()
    {
        return $this->hasOne(ActivityProgress::class, 'activity_id')->latest();
    }
    // Boot method to auto-calculate duration
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($activity) {
            if ($activity->start_time && $activity->end_time) {
                $activity->duration_days = $activity->calculateDuration();
            }
        });
    }
}