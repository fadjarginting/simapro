<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use League\CommonMark\Node\Block\Document;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // relasi ke document
    public function documents()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    // scope untuk filtering dan searching
    public function scopeFilter($query, array $filters)
    {
        // Search by name or email
        $query->when($filters['search'] ?? null, function ($q, $search) {
            return $q->where(function ($subquery) use ($search) {
                $subquery->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        });

        // Filter by role
        $query->when($filters['role'] ?? null, function ($q, $role) {
            return $q->whereHas('roles', function ($subquery) use ($role) {
                $subquery->where('name', $role);
            });
        });
    }
}
