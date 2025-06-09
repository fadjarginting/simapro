<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Discipline extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $fillable = [
        'name',
        'description',
    ];
    /**
     * Get the users associated with the discipline.
     */
    public function users()
    {
        return $this->hasMany('App\Models\User');
    }
    /**
     * Get the work  associated with the discipline.
     */
    public function works()
    {
        return $this->hasMany('App\Models\Work');
    }
    /**
     * Get the activities associated with the discipline.
     */
    public function activities()
    {
        return $this->hasMany('App\Models\Activity');
    }
    /**
     * Get the projects associated with the discipline.
     */
    public function projects()
    {
        return $this->hasMany('App\Models\Project');
    }
}
