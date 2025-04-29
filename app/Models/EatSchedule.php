<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EatSchedule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'meal_type',
        'scheduled_time',
        'food_items',
        'calories',
        'notes',
        'is_completed'
    ];
    
    protected $casts = [
        'scheduled_time' => 'datetime',
        'is_completed' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}