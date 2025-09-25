<?php

namespace App\Http\Controllers;

use App\Models\Eat;
use App\Models\Activity;
use App\Models\EatApproval;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Store a newly created EAT in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_id' => 'required|exists:works,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activities' => 'required|array|min:1',
            'activities.*.activity_name' => 'required|string|max:255',
            'activities.*.discipline_id' => 'required|exists:disciplines,id',
            'activities.*.activity_description' => 'nullable|string',
            'activities.*.start_date' => 'required|date',
            'activities.*.end_date' => 'required|date|after_or_equal:activities.*.start_date',
            'activities.*.pic_ids' => 'required|array|min:1',
            'activities.*.pic_ids.*' => 'exists:users,id',
            'approvals' => 'required|array|min:1',
            'approvals.*.approver_id' => 'required|exists:users,id',
            'isDraft' => 'boolean',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Create main EAT record
            $eat = Eat::create([
            'work_id' => $validated['work_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $request->input('isDraft', false) ? 'draft' : 'submitted',
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            ]);

            // Create activities
            foreach ($validated['activities'] as $activityData) {
            $activity = $eat->activities()->create([
                'discipline_id' => $activityData['discipline_id'],
                'activity_name' => $activityData['activity_name'],
                'activity_description' => $activityData['activity_description'] ?? null,
                'start_date' => $activityData['start_date'],
                'end_date' => $activityData['end_date'],
            ]);

            // Attach PICs to activity
            if (!empty($activityData['pic_ids'])) {
                $activity->pics()->attach($activityData['pic_ids']);
            }
            }

            // Create approvals (only if not draft)
            if (!$request->input('isDraft', false)) {
            foreach ($validated['approvals'] as $approvalData) {
                $eat->approvals()->create([
                'approver_id' => $approvalData['approver_id'],
                'status' => 'pending',
                ]);
            }
            }
            return redirect()->back()->with('success', 'EAT berhasil dibuat!')->with('eatId', $eat->id);
        });
    }

    /**
     * Update the specified EAT in storage.
     */
    public function update(Request $request, Eat $eat)
    {
        // Only allow updating if EAT is in draft or rejected status
        if (!in_array($eat->status, ['draft', 'rejected'])) {
            return redirect()->back()->with('error', 'EAT ini tidak dapat diupdate!');
        }

        $validated = $request->validate([
            'work_id' => 'required|exists:works,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'activities' => 'required|array|min:1',
            'activities.*.id' => 'nullable|exists:activities,id',
            'activities.*.activity_name' => 'required|string|max:255',
            'activities.*.discipline_id' => 'required|exists:disciplines,id',
            'activities.*.activity_description' => 'nullable|string',
            'activities.*.start_date' => 'required|date',
            'activities.*.end_date' => 'required|date|after_or_equal:activities.*.start_date',
            'activities.*.pic_ids' => 'required|array|min:1',
            'activities.*.pic_ids.*' => 'exists:users,id',
            'approvals' => 'required|array|min:1',
            'approvals.*.approver_id' => 'required|exists:users,id',
            'isDraft' => 'boolean',
        ]);

        

        return DB::transaction(function () use ($validated, $request, $eat) {
            // Update main EAT record
            $eat->update([
            'work_id' => $validated['work_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $request->input('isDraft', false) ? 'draft' : 'submitted',
            'updated_by' => Auth::id(),
            ]);

            // Get existing activity IDs
            $existingActivityIds = $eat->activities()->pluck('id')->toArray();
            $updatedActivityIds = [];

            // Update or create activities
            foreach ($validated['activities'] as $activityData) {
            if (isset($activityData['id']) && in_array($activityData['id'], $existingActivityIds)) {
                // Update existing activity
                $activity = Activity::find($activityData['id']);
                $activity->update([
                'discipline_id' => $activityData['discipline_id'],
                'activity_name' => $activityData['activity_name'],
                'activity_description' => $activityData['activity_description'] ?? null,
                'start_date' => $activityData['start_date'],
                'end_date' => $activityData['end_date'],
                ]);
                $updatedActivityIds[] = $activity->id;
            } else {
                // Create new activity
                $activity = $eat->activities()->create([
                'discipline_id' => $activityData['discipline_id'],
                'activity_name' => $activityData['activity_name'],
                'activity_description' => $activityData['activity_description'] ?? null,
                'start_date' => $activityData['start_date'],
                'end_date' => $activityData['end_date'],
                ]);
                $updatedActivityIds[] = $activity->id;
            }

            // Sync PICs
            if (!empty($activityData['pic_ids'])) {
                $activity->pics()->sync($activityData['pic_ids']);
            }
            }

            // Delete activities that are no longer in the request
            $activitiesToDelete = array_diff($existingActivityIds, $updatedActivityIds);
            if (!empty($activitiesToDelete)) {
            Activity::whereIn('id', $activitiesToDelete)->delete();
            }

            // Update approvals (only if not draft)
            $eat->approvals()->delete(); // Remove old approvals
            if (!$request->input('isDraft', false)) {
            foreach ($validated['approvals'] as $approvalData) {
                $eat->approvals()->create([
                'approver_id' => $approvalData['approver_id'],
                'status' => 'pending',
                ]);
            }
            }

            $slug = $eat->work->slug ?? null;

            return redirect()->route('works.show', $slug)->with('success', 'EAT berhasil diperbarui!');
        });
    }

    /**
     * Remove the specified EAT from storage.
     */
    public function destroy(Eat $eat)
    {
        // Only allow deletion if EAT is in draft status
        if ($eat->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya EAT dengan status draft yang dapat dihapus!');
        }

        $eat->delete();

        return redirect()->route('eat.index')->with('success', 'EAT berhasil dihapus!');
    }

    /**
     * Approve an EAT.
     */
    public function approve(Request $request, Eat $eat)
    {
        // Tambahkan pengecekan eksistensi record
        if (!$eat->exists) {
            return redirect()->back()->with('error', 'EAT tidak ditemukan!');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Ambil record approval dari tabel pivot EatApproval
        $approval = $eat->approvals()->where('approver_id', Auth::id())->first();

        if (!$approval) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk memproses EAT ini!');
        }

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'EAT ini sudah diproses sebelumnya!');
        }

        // Proses approval berdasarkan status yang dikirim
        if ($validated['status'] === 'approved') {
            $approval->approve($validated['notes']);
        } else {
            $approval->reject($validated['notes']);
        }

        // Refresh data approvals
        $approvals = $eat->approvals()->get();
        // Update status EAT berdasarkan hasil approval
        if ($approvals->contains('status', 'rejected')) {
            $eat->update(['status' => 'rejected']);
        } elseif ($approvals->every(fn($a) => $a->status === 'approved')) {
            $eat->update(['status' => 'approved']);
        }
        return response()->json([
            'success' => true,
            'message' => 'EAT berhasil diproses!',
        ], 200);
    }

    /**
     * Reject an EAT.
     */
    public function reject(Request $request, Eat $eat)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        $approval = $eat->approvals()->where('approver_id', Auth::id())->first();

        if (!$approval) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk menolak EAT ini!');
        }

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'EAT ini sudah diproses sebelumnya!');
        }

        $approval->reject($validated['notes']);
        $eat->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'EAT berhasil ditolak!');
    }

    /**
     * Get EATs for API usage.
     */
    public function apiIndex()
    {
        $eats = Eat::with([
            'activities.discipline',
            'activities.pics',
            'approvals.approver'
        ])->get();

        return response()->json($eats);
    }

    /**
     * Get EAT by work ID for API usage.
     */
    public function getEATByWork($workId)
    {
        $eat = Eat::where('work_id', $workId)
            ->with([
                'work:id,description,slug',
                'activities.discipline:id,name',
                'activities.pics:id,name,email',
                'activities.latestProgress:id,activity_id,progress_description,progress_percentage,reported_by',
                'activities.latestProgress.reporter:id,name,email',
                'approvals.approver:id,name,email,discipline_id',
                'approvals.approver.discipline:id,name',
            ])
            ->first();

        if (!$eat) {
            return response()->json(['message' => 'EAT not found'], 404);
        }

        // Map the data to a more frontend-friendly format
        $mappedEat = [
            'id' => $eat->id,
            'work_id' => $eat->work_id,
            'work' => [
                'description' => $eat->work->description,
                'slug' => $eat->work->slug,
            ],
            'start_date' => $eat->start_date,
            'end_date' => $eat->end_date,
            'status' => $eat->status,
            'activities' => $eat->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'eat_id' => $activity->eat_id,
                    'activity_name' => $activity->activity_name,
                    'discipline_id' => $activity->discipline_id,
                    'discipline' => $activity->discipline->name,
                    'start_date' => $activity->start_date,
                    'end_date' => $activity->end_date,
                    'pics' => $activity->pics->map(fn($pic) => ['id' => $pic->id,'name' => $pic->name, 'email' => $pic->email]),
                    'latest_progress' => $activity->latestProgress ? [
                        'description' => $activity->latestProgress->progress_description,
                        'percentage' => $activity->latestProgress->progress_percentage,
                        'reporter' => $activity->latestProgress->reporter->name,
                        'updated_at' => $activity->latestProgress->updated_at,
                    ] : null,
                ];
            }),
            'approvals' => $eat->approvals->map(function ($approval) {
                return [
                    'eat_id' => $approval->eat_id,
                    'approver_id' => $approval->approver_id,
                    'approver' => $approval->approver->name,
                    'discipline_id' => $approval->approver->discipline_id,
                    'discipline' => $approval->approver->discipline->name,
                    'status' => $approval->status,
                    'notes' => $approval->notes,
                ];
            }),
        ];

        return response()->json($mappedEat);
    }

    /**
     * Get all disciplines for dropdown.
     */
    public function getDisciplines()
    {
        $disciplines = Discipline::all();
        return response()->json($disciplines);
    }

    /**
     * Get all users for dropdown.
     */
    public function getUsers()
    {
        $users = User::with('discipline')->get();
        return response()->json($users);
    }

    /**
     * Display the specified resource by Work ID.
     */
    public function showByWorkId(Work $work)
    {
        $user = auth()->user();

        // Eager load relationships for efficiency
        $eat = Eat::with([
            'activities.pics',
            'activities.latestProgress',
            'activities.discipline',
            'approvals.approver',
            'approvals.discipline'
        ])->where('work_id', $work->id)->first();

        // If EAT does not exist, return 404
        if (!$eat) {
            return response()->json(null, 404);
        }

        // Authorization Check
        $isLeadEngineer = $work->lead_engineer_id === $user->id;
        $isPic = $eat->activities->flatMap->pics->pluck('id')->contains($user->id);
        $isApprover = $eat->approvals->pluck('approver_id')->contains($user->id);
        $hasGeneralViewPermission = $user->can('manajemen_pekerjaan.view');

        if (!$isLeadEngineer && !$isPic && !$isApprover && !$hasGeneralViewPermission) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk mengakses EAT ini.'], 403);
        }

        $eatData = $this->formatEatData($eat);

        return response()->json($eatData);
    }

    private function formatEatData(Eat $eat)
    {
        // Format data as needed for the response
        return [
            'id' => $eat->id,
            'work_id' => $eat->work_id,
            'start_date' => $eat->start_date,
            'end_date' => $eat->end_date,
            'status' => $eat->status,
            'activities' => $eat->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'activity_name' => $activity->activity_name,
                    'discipline_id' => $activity->discipline_id,
                    'activity_description' => $activity->activity_description,
                    'start_date' => $activity->start_date,
                    'end_date' => $activity->end_date,
                    'pics' => $activity->pics->map(fn($pic) => ['id' => $pic->id, 'name' => $pic->name]),
                    'latest_progress' => $activity->latestProgress ? [
                        'description' => $activity->latestProgress->progress_description,
                        'percentage' => $activity->latestProgress->progress_percentage,
                        'reporter' => $activity->latestProgress->reporter->name,
                        'updated_at' => $activity->latestProgress->updated_at,
                    ] : null,
                ];
            }),
            'approvals' => $eat->approvals->map(function ($approval) {
                return [
                    'approver_id' => $approval->approver_id,
                    'status' => $approval->status,
                    'notes' => $approval->notes,
                ];
            }),
        ];
    }
}