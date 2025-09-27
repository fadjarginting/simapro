<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Work;
use Inertia\Inertia;
use App\Models\Noted;
use App\Models\Plant;
use App\Enums\WorkType;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Progress;
use App\Enums\CurrentPhase;
use App\Enums\WorkPriority;
use Illuminate\Support\Str;
use App\Enums\ProjectStatus;
use Illuminate\Http\Request;
use App\Enums\RequestCategory;
use App\Enums\VerificationStatus;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreWorkRequest;
use App\Http\Requests\WorkIndexRequest;
use App\Http\Requests\UpdateWorkRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WorkIndexRequest $request)
    {
        // Get filter options
        $filterOptions = $this->getFilterOptions();

        // Get filters from request
        $filters = $request->getFilters();
        
        // Get works with filters applied
        $worksQuery = Work::filterAndSearch($request->validated());
        
        // Hide finished works by default unless the filter explicitly includes finished status
        if (!isset($filters['project_status']) || $filters['project_status'] !== ProjectStatus::FINISH->value) {
            $worksQuery->where('project_status', '<>', ProjectStatus::FINISH->value);
        }
        
        // Get statistics before pagination
        $statistics = [
            'total'    => Work::count(),
            'filtered' => $worksQuery->count(),
        ];
        
        // Apply pagination
        $works = $worksQuery->paginate(
            $request->get('per_page', 10),
            ['*'],
            'page',
            $request->get('page', 1)
        )->appends($request->query());
        
        $statistics['showing'] = $works->count();

        return Inertia::render('WorksManagement/Index', [
            'works'      => $works,
            'filters'    => $filters,
            'statistics' => $statistics,
            ...$filterOptions
        ]);
    }

    private function getFilterOptions(): array
    {
        return [
            'plants' => Plant::select('id', 'name')->get(),
            'requestCategories' => RequestCategory::cases(),
            'workPriorities' => WorkPriority::cases(),
            'workTypes' => WorkType::cases(),
            'verificationStatuses' => VerificationStatus::cases(),
            'projectStatuses' => ProjectStatus::cases(),
            'currentPhases' => CurrentPhase::cases(),
        ];
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('WorksManagement/Create', [
            'plants' => Plant::select('id', 'name')->get(),
            'notes' => Note::select('id', 'content')->get(),
            'workPriorities' => WorkPriority::cases(),
            'workTypes' => WorkType::cases(),
            'requestCategories' => RequestCategory::cases(),
            'verificationStatuses' => VerificationStatus::cases(),
            'projectStatuses' => ProjectStatus::cases(),
            'currentPhases' => CurrentPhase::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkRequest $request)
    {
        try {
            $work = $this->createWork($request->validated());
            
            return redirect()
                ->route('works.index')
                ->with('success', 'Data pekerjaan baru berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Create a new work record with validated data.
     */
    private function createWork(array $validatedData): Work
    {
        $workData = $this->prepareWorkData($validatedData);
        
        return Work::create($workData);
    }

    private function prepareWorkData(array $validatedData, ?Work $existingWork = null): array
    {
        $workData = collect($validatedData)->only([
            'erf_number', 'description', 'plant_id', 'requester_unit',
            'work_priority', 'work_type', 'request_category', 'entry_date',
            'erf_approved_date', 'clarification_date', 'erf_validated_date',
            'initiating_target_date', 'executing_start_date', 'executing_target_date',
            'executing_actual_date', 'verification_status', 'project_status',
            'current_phase', 'lead_engineer_id', 'note_id'
        ])->toArray();

        // Generate slug for new records or if description changed
        if (!$existingWork || $existingWork->description !== $validatedData['description']) {
            $workData['slug'] = $this->generateUniqueSlug(
                $validatedData['description'], 
                $existingWork?->id
            );
        }

        // Set user info
        if (!$existingWork) {
            $workData['created_by'] = Auth::id();
        }
        $workData['updated_by'] = Auth::id();

        // Set default values for new records
        if (!$existingWork) {
            $workData['project_status'] ??= ProjectStatus::NOT_STARTED->value;
            $workData['current_phase'] ??= CurrentPhase::NOT_STARTED->value;
            $workData['verification_status'] ??= VerificationStatus::BELUM->value;
        }

        return $workData;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Work $work)
    {
        return Inertia::render('WorksManagement/Edit', [
            'work' => $work,
            'plants' => Plant::select('id', 'name')->get(),
            'notes' => Note::select('id', 'content')->get(),
            'workPriorities' => WorkPriority::cases(),
            'workTypes' => WorkType::cases(),
            'requestCategories' => RequestCategory::cases(),
            'verificationStatuses' => VerificationStatus::cases(),
            'projectStatuses' => ProjectStatus::cases(),
            'currentPhases' => CurrentPhase::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkRequest $request, Work $work)
    {
        try {
            $workData = $this->prepareWorkData($request->validated(), $work);
            $work->update($workData);
            
            return redirect()
                ->route('works.index')
                ->with('success', 'Data pekerjaan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }

    private function generateUniqueSlug(string $description, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($description);
        $slug = $baseSlug;
        $counter = 1;

        $query = Work::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            
            $query = Work::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        return $slug;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work)
    {
        try {
            $work->delete();
            
            return redirect()
                ->route('works.index')
                ->with('success', 'Data pekerjaan berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function detail(Work $work)
    {
        // Load only the necessary relationships
        $work->load([
            'plant:id,name',
            'leadEngineer:id,name',
            'creator:id,name',
            'note:id,content',
        ]);

        // documents
        $documents = $work->documents()
            ->select('id', 'document_name', 'file_url', 'uploaded_at', 'uploaded_by')
            ->with('uploadedBy:id,name')
            ->get();

        // notes
        $notes = Note::select('id', 'content')
            ->get();

        return Inertia::render('WorksManagement/Detail/WorkDetailMain', [
            'work' => $work,
            'documents' => $documents,
            'notes' => $notes,
            // Only include essential permissions for display
        ]);
    }

    public function updateBasicInfo(Request $request, Work $work)
    {
        $request->validate([
            'note_id' => 'nullable|exists:noted,id',
            'lead_engineer_id' => 'nullable|exists:users,id'
        ]);

        // Update catatan, hanya jika pengguna adalah lead engineer
        if ($request->has('note_id') && Auth::id() === $work->lead_engineer_id) {
            $work->update(['note_id' => $request->note_id]);
        }

        // Update lead engineer
        if ($request->has('lead_engineer_id')) {
            $work->update(['lead_engineer_id' => $request->lead_engineer_id]);
        }

        return redirect()->back()->with('success', 'Informasi berhasil diperbarui');
    }

   
    /**
     * Updates the status fields of a given Work model based on validated request data.
     *
     * This method performs the following actions:
     * - Validates the incoming request to ensure the provided fields meet their required formats:
     *   - verification_status: must be one of "Belum Verifikasi", "In Progress Verifikasi", "Finish Verifikasi".
     *   - project_status: must be one of "Not Started", "In Progress", "Finish", "On Hold", "Cancelled".
     *   - current_phase: must be one of "Not started", "Initiating", "Executing", "Closing", "Hold", "Reject".
     * - Uses the validated input to update the corresponding attributes of the provided Work model.
     * - Redirects back to the previous page with a success message upon successful update.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing user input.
     * @param \App\Models\Work $work The Work model instance to update.
     * @return \Illuminate\Http\RedirectResponse A redirect response back to the previous page with a success notification.
     */
    public function updateStatus(Request $request, Work $work)
    {
        $request->validate([
            'verification_status' => ['required', Rule::in(VerificationStatus::cases())],
            'project_status' => ['required', Rule::in(ProjectStatus::cases())],
            'current_phase' => ['required', Rule::in(CurrentPhase::cases())]
        ]);

        $isAdmin = Auth::user()->hasRole('Admin');


        // Hanya admin atau lead engineer yang dapat mengubah status
        if (!$isAdmin && Auth::id() !== $work->lead_engineer_id) {
            return back()->with('error', 'Hanya admin atau lead engineer yang dapat mengubah status.', 400);
        }

        $work->update($request->only([
            'verification_status',
            'project_status', 
            'current_phase'
        ]));

        return back()->with('success', 'Status berhasil diperbarui');
    }


    /**
     * Updates the timeline fields of a given Work model.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the timeline data.
     * @param \App\Models\Work $work The Work model instance to update.
     * @return \Illuminate\Http\RedirectResponse A redirect response back to the previous page with a success notification.
     */
    public function updateTimeline(Request $request, Work $work)
    {
        // Validasi input
        $validatedData = $request->validate([
            'field' => 'required|string|in:entry_date,erf_approved_date,clarification_date,erf_validated_date,initiating_target_date,executing_start_date,executing_target_date,executing_actual_date',
            'date' => 'nullable|date',
            'work_id' => 'required|exists:works,id'
        ]);

        // validasi user
        $isAdmin = Auth::user()->hasRole('Admin');
        $isWorkLead = Auth::id() === $work->lead_engineer_id;

        if (!$isAdmin && !$isWorkLead) {
            return back()->with('error', 'Hanya admin atau lead engineer yang dapat mengubah timeline.', 403);
        }

        // Pastikan work_id sesuai dengan work yang diupdate
        if ($work->id != $validatedData['work_id']) {
            return back()->withErrors(['error' => 'Invalid work ID']);
        }

        $fieldToUpdate = $validatedData['field'];
        $dateValue = $validatedData['date'] ? new \DateTime($validatedData['date']) : null;

        $dateFieldsOrder = [
            'entry_date', 'erf_approved_date', 'clarification_date', 'erf_validated_date',
            'initiating_target_date', 'executing_start_date', 'executing_target_date', 'executing_actual_date'
        ];

        $fieldIndex = array_search($fieldToUpdate, $dateFieldsOrder);

        // Jika user mencoba mengisi tanggal baru
        if ($dateValue !== null) {
            // Periksa apakah tanggal sebelumnya sudah terisi
            if ($fieldIndex > 0) {
                $previousField = $dateFieldsOrder[$fieldIndex - 1];
                if (is_null($work->$previousField)) {
                    return back()->with('error', 'Harap isi tanggal sebelumnya terlebih dahulu.');
                }
            }
        } else { // Jika user mencoba mengosongkan tanggal
            // Periksa apakah ada tanggal setelahnya yang sudah terisi
            if ($fieldIndex < count($dateFieldsOrder) - 1) {
                for ($i = $fieldIndex + 1; $i < count($dateFieldsOrder); $i++) {
                    $nextField = $dateFieldsOrder[$i];
                    if (!is_null($work->$nextField)) {
                        return back()->with('error', 'Tidak dapat mengosongkan tanggal ini karena tanggal berikutnya sudah terisi.');
                    }
                }
            }
        }

        // Update field yang spesifik
        $work->update([
            $fieldToUpdate => $dateValue
        ]);

        return back()->with('success', 'Timeline berhasil diperbarui.');
    }

    /**
     * Display the user's works.
     */
    public function myWorks(WorkIndexRequest $request)
    {
        // Get filter options
        $filterOptions = $this->getFilterOptions();

        // Get filters from request
        $filters = $request->getFilters();
        
        $userId = Auth::id();
        $user = Auth::user();
        $userRole = $user->role; // Assuming role is stored in user model

        if ($userRole === 'user') {
            // For regular users: only show works where they are PIC in EAT activities
            // Include FINISHED works (don't hide them)
            $worksQuery = Work::whereHas('eat.activities.pics', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            });

            // Apply search and filters from the request
            $worksQuery->filterAndSearch($request->validated());

            // Get statistics
            $statistics = [
                'total'    => Work::whereHas('eat.activities.pics', function ($query) use ($userId) {
                                $query->where('users.id', $userId);
                            })->count(),
                'filtered' => $worksQuery->count(),
            ];

            // Apply pagination
            $works = $worksQuery->paginate(
                $request->get('per_page', 10),
                ['*'],
                'page', 
                $request->get('page', 1)
            )->appends($request->query());

            $statistics['showing'] = $works->count();

            return Inertia::render('MyWorks/Index', [
                'works'      => $works,
                'filters'    => $filters,
                'statistics' => $statistics,
                'user_role'  => $userRole,
                ...$filterOptions
            ]);

        } elseif ($userRole === 'lead') {
            // For lead users: show all works categorized by role
            // Include FINISHED works (don't hide them)
            
            // Get works as Lead Engineer
            $worksAsLead = Work::where('lead_engineer_id', $userId)
                ->filterAndSearch($request->validated())
                ->get();

            // Get works as PIC
            $worksAsPIC = Work::whereHas('eat.activities.pics', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })->filterAndSearch($request->validated())->get();

            // Get works as Approver
            $worksAsApprover = Work::whereHas('eat.approvals', function ($query) use ($userId) {
                $query->where('approver_id', $userId);
            })->filterAndSearch($request->validated())->get();

            // Combine all works and remove duplicates
            $allWorkIds = collect()
                ->merge($worksAsLead->pluck('id'))
                ->merge($worksAsPIC->pluck('id'))
                ->merge($worksAsApprover->pluck('id'))
                ->unique();

            // Get paginated results from all combined works
            $worksQuery = Work::whereIn('id', $allWorkIds);
            $worksQuery->filterAndSearch($request->validated());

            // Get statistics
            $statistics = [
                'total' => $allWorkIds->count(),
                'filtered' => $worksQuery->count(),
                'categories' => [
                    'lead' => $worksAsLead->count(),
                    'pic' => $worksAsPIC->count(),
                    'approver' => $worksAsApprover->count(),
                ]
            ];

            // Apply pagination
            $works = $worksQuery->paginate(
                $request->get('per_page', 10),
                ['*'],
                'page', 
                $request->get('page', 1)
            )->appends($request->query());

            $statistics['showing'] = $works->count();

            // Categorize each work for the lead user
            $categorizedWorks = $works->map(function ($work) use ($userId) {
                $categories = [];
                
                if ($work->lead_engineer_id == $userId) {
                    $categories[] = 'lead';
                }
                
                if ($work->eat && $work->eat->activities) {
                    foreach ($work->eat->activities as $activity) {
                        if ($activity->pics && $activity->pics->contains('id', $userId)) {
                            $categories[] = 'pic';
                            break;
                        }
                    }
                }
                
                if ($work->eat && $work->eat->approvals && $work->eat->approvals->contains('approver_id', $userId)) {
                    $categories[] = 'approver';
                }
                
                $work->user_categories = array_unique($categories);
                return $work;
            });

            return Inertia::render('MyWorks/Index', [
                'works'      => $works,
                'categorized_works' => $categorizedWorks,
                'filters'    => $filters,
                'statistics' => $statistics,
                'user_role'  => $userRole,
                'work_categories' => [
                    'lead' => $worksAsLead,
                    'pic' => $worksAsPIC,
                    'approver' => $worksAsApprover,
                ],
                ...$filterOptions
            ]);
        }

        // Fallback for other roles (original logic but without hiding finished works)
        $worksQuery = Work::where(function ($query) use ($userId) {
            $query->where('lead_engineer_id', $userId)
                  ->orWhereHas('eat.activities.pics', function ($subQuery) use ($userId) {
                      $subQuery->where('users.id', $userId);
                  })
                  ->orWhereHas('eat.approvals', function ($subQuery) use ($userId) {
                      $subQuery->where('approver_id', $userId);
                  });
        });

        // Apply search and filters from the request
        $worksQuery->filterAndSearch($request->validated());
        
        // Get statistics before pagination
        $statistics = [
            'total'    => Work::where(function ($query) use ($userId) {
                            $query->where('lead_engineer_id', $userId)
                                ->orWhereHas('eat.activities.pics', function ($subQuery) use ($userId) {
                                    $subQuery->where('users.id', $userId);
                                })
                                ->orWhereHas('eat.approvals', function ($subQuery) use ($userId) {
                                    $subQuery->where('approver_id', $userId);
                                });
                        })->count(),
            'filtered' => $worksQuery->count(),
        ];
        
        // Apply pagination
        $works = $worksQuery->paginate(
            $request->get('per_page', 10),
            ['*'],
            'page', 
            $request->get('page', 1)
        )->appends($request->query());
        
        $statistics['showing'] = $works->count();

        return Inertia::render('MyWorks/Index', [
            'works'      => $works,
            'filters'    => $filters,
            'statistics' => $statistics,
            'user_role'  => $userRole ?? 'default',
            ...$filterOptions
        ]);
    }

}