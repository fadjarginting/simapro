<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EatApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'eat_id',
        'approver_id',
        'status',
        'notes',
        'approval_date',
    ];

    protected $casts = [
        'approval_date' => 'datetime',
    ];

    // Relationships
    public function eat(): BelongsTo
    {
        return $this->belongsTo(Eat::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function approve($notes = null)
    {
        $this->update([
            'status' => 'approved',
            'notes' => $notes,
            'approval_date' => now(),
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $notes,
            'approval_date' => now(),
        ]);
    }
}