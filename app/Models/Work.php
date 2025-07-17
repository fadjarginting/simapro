<?php

namespace App\Models;

use App\Models\Plant;
use App\Enums\WorkType;
use App\Enums\CurrentPhase;
use App\Enums\WorkPriority;
use App\Enums\ProjectStatus;
use App\Enums\RequestCategory;
use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Work extends Model
{
    use HasFactory;
    protected $fillable = [
        'erf_number',
        'description',
        'entry_date',
        'plant_id',
        'requester_unit',
        'work_priority',
        'work_type',
        'request_category',
        'erf_approved_date',
        'clarification_date',
        'erf_validated_date',
        'initiating_target_date',
        'executing_start_date',
        'executing_target_date',
        'executing_actual_date',
        'verification_status',
        'project_status',
        'current_phase',
        'note_id',
        'lead_engineer_id',
        'created_by',
        'updated_by',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entry_date' => 'date',
        'erf_approved_date' => 'date',
        'clarification_date' => 'date',
        'erf_validated_date' => 'date',
        'initiating_target_date' => 'date',
        'executing_start_date' => 'date',
        'executing_target_date' => 'date',
        'executing_actual_date' => 'date',
        // Kolom enum akan di-handle sebagai string secara default.
        // Jika Anda menggunakan PHP 8.1+ Enums, Anda bisa melakukan casting ke Enum class di sini.
        // Contoh: 'work_priority' => \App\Enums\WorkPriority::class,
        'work_priority' => WorkPriority::class,
        'work_type' => WorkType::class,
        'request_category' => RequestCategory::class,
        'verification_status' => VerificationStatus::class,
        'project_status' => ProjectStatus::class,
        'current_phase' => CurrentPhase::class,
    ];

    /**
     * Get the plant that owns the work.
     */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }

    /**
     * Get the note associated with the work.
     * Asumsi nama model adalah 'Note' untuk tabel 'noted'.
     * Sesuaikan nama model jika berbeda.
     */
    public function note(): BelongsTo
    {
        // Perhatikan: tabel di migrasi adalah 'noted'.
        // Jika modelnya adalah 'Note', Laravel akan mencari tabel 'notes' secara default.
        // Jika nama tabel tidak konvensional, Anda mungkin perlu menentukannya di model Note.
        return $this->belongsTo(Note::class, 'note_id');
    }

    /**
     * Get the lead engineer for the work.
     * PERHATIAN: Migrasi menggunakan onDelete('cascade') untuk relasi ini.
     * Jika user lead engineer dihapus, semua pekerjaan yang terkait dengannya akan ikut terhapus.
     * Pertimbangkan untuk menggunakan onDelete('set null') jika ini bukan perilaku yang diinginkan.
     */
    public function leadEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_engineer_id');
    }

    // relasi tabel eat satu pekerjaan punya satu eat
    public function eat(): BelongsTo
    {
        return $this->belongsTo(Eat::class, 'id', 'work_id');
    }

    /**
     * Get the user who created the work.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the work.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug'; // Menggunakan slug untuk route model binding
    }
    // Scopes for filtering and searching
    public function scopeFilterAndSearch(Builder $query, array $filters = []): Builder
    {
        return $query
            ->with([
                'plant:id,name', 
                'leadEngineer:id,name'
            ])
            ->when($filters['search'] ?? null, fn($q, $search) => $this->applySearch($q, $search))
            ->when($filters['plant_id'] ?? null, fn($q, $plantId) => $q->where('plant_id', $plantId))
            ->when($filters['work_priority'] ?? null, fn($q, $priority) => $q->where('work_priority', $priority))
            ->when($filters['work_type'] ?? null, fn($q, $type) => $q->where('work_type', $type))
            ->when($filters['request_category'] ?? null, fn($q, $category) => $q->where('request_category', $category))
            ->when($filters['verification_status'] ?? null, fn($q, $status) => $q->where('verification_status', $status))
            ->when($filters['project_status'] ?? null, fn($q, $status) => $q->where('project_status', $status))
            ->when($filters['current_phase'] ?? null, fn($q, $phase) => $q->where('current_phase', $phase))
            ->when($filters['start_date'] ?? null, fn($q, $date) => $q->where('entry_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn($q, $date) => $q->where('entry_date', '<=', $date))
            ->when($filters['target_start_date'] ?? null, fn($q, $date) => $q->where('executing_target_date', '>=', $date))
            ->when($filters['target_end_date'] ?? null, fn($q, $date) => $q->where('executing_target_date', '<=', $date))
            ->orderBy(
                $this->validateSortField($filters['sort_by'] ?? 'erf_number'),
                $filters['sort_order'] ?? 'desc'
            );
    }

    private function applySearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('erf_number', 'like', "%{$search}%")
              ->orWhere('requester_unit', 'like', "%{$search}%")
              ->orWhereHas('plant', function ($plantQuery) use ($search) {
                  $plantQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    private function validateSortField(string $field): string
    {
        $allowedFields = [
            'erf_number', 'description', 'entry_date', 'work_priority',
            'work_type', 'project_status', 'current_phase', 'executing_target_date'
        ];

        return in_array($field, $allowedFields) ? $field : 'erf_number';
    }

    // Additional utility scopes
    public function scopeByPlant(Builder $query, int $plantId): Builder
    {
        return $query->where('plant_id', $plantId);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('work_priority', $priority);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('project_status', $status);
    }

    public function scopeInDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        return $query->when($startDate, fn($q) => $q->where('entry_date', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->where('entry_date', '<=', $endDate));
    }
    

    /**
     * Get the documents for the work.
    */
    public function documents(): HasMany
    {
        return $this->hasMany(ErfDocument::class);
    }
}
