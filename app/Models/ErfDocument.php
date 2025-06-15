<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ErfDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'document_name',
        'file_url',
        'uploaded_at',
        'uploaded_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the work that owns the document.
     */
    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeAttribute(): string
    {
        $filePath = str_replace('/storage/', '', $this->file_url);
        
        if (Storage::disk('public')->exists($filePath)) {
            $bytes = Storage::disk('public')->size($filePath);
            return $this->formatBytes($bytes);
        }
        
        return 'Unknown';
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get file extension from file URL.
     */
    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->file_url, PATHINFO_EXTENSION);
    }
}