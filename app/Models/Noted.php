<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Noted extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'noted_code'];
    /**
     * Search scope for the model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('slug', 'like', "%{$search}%")
            ->orWhere('noted', 'like', "%{$search}%");
    }
}
