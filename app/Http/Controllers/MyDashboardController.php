<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\WorkType;
use App\Models\Work;
use App\Models\ActivityPic;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MyDashboardController extends Controller
{
    /**
     * Menampilkan dasbor yang dipersonalisasi untuk pengguna yang sedang login.
     * Dashboard berbeda berdasarkan role:
     * - Lead Engineer: Menampilkan data Work yang dia lead
     * - User biasa: Menampilkan data Activity yang dia handle sebagai PIC
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Cek apakah user memiliki pekerjaan sebagai lead engineer
        $hasLeadWork = Work::where('lead_engineer_id', $user->id)->exists();
        
        // Cek apakah user memiliki aktivitas sebagai PIC
        $hasPicActivities = ActivityPic::where('user_id', $user->id)->exists();

        if ($hasLeadWork && $hasPicActivities) {
            // Jika user adalah lead engineer DAN PIC, tampilkan keduanya
            return $this->getCombinedDashboard($user->id);
        } elseif ($hasLeadWork) {
            // Dashboard untuk Lead Engineer saja
            return $this->getLeadEngineerDashboard($user->id);
        } elseif ($hasPicActivities) {
            // Dashboard untuk User PIC saja
            return $this->getUserPicDashboard($user->id);
        } else {
            // User belum memiliki pekerjaan atau aktivitas
            return $this->getEmptyDashboard();
        }
    }

    /**
     * Dashboard untuk user yang memiliki kedua role
     */
    private function getCombinedDashboard($userId)
    {
        // Data Lead Engineer
        $baseQuery = Work::where('lead_engineer_id', $userId);
        $workloadSummary = $this->getWorkloadSummary(clone $baseQuery);
        $priorityTasks = $this->getPriorityTasks(clone $baseQuery);
        $workBreakdown = $this->getWorkBreakdown(clone $baseQuery);

        // Data PIC
        $activitySummary = $this->getActivityPicSummary($userId);
        $priorityActivities = $this->getPriorityActivities($userId);
        $activityBreakdown = $this->getActivityBreakdown($userId);

        return Inertia::render('MyDashboard/MyDashboard', [
            'userRole' => 'combined',
            // Lead Engineer Data
            'workloadSummary' => $workloadSummary,
            'priorityTasks' => $priorityTasks,
            'workBreakdown' => $workBreakdown,
            // PIC Data
            'activitySummary' => $activitySummary,
            'priorityActivities' => $priorityActivities,
            'activityBreakdown' => $activityBreakdown,
        ]);
    }

    /**
     * Dashboard khusus untuk Lead Engineer
     */
    private function getLeadEngineerDashboard($userId)
    {
        $baseQuery = Work::where('lead_engineer_id', $userId);

        $workloadSummary = $this->getWorkloadSummary(clone $baseQuery);
        $priorityTasks = $this->getPriorityTasks(clone $baseQuery);
        $workBreakdown = $this->getWorkBreakdown(clone $baseQuery);

        return Inertia::render('MyDashboard/MyDashboard', [
            'userRole' => 'lead_engineer',
            'workloadSummary' => $workloadSummary,
            'priorityTasks' => $priorityTasks,
            'workBreakdown' => $workBreakdown,
        ]);
    }

    /**
     * Dashboard khusus untuk User PIC
     */
    private function getUserPicDashboard($userId)
    {
        $activitySummary = $this->getActivityPicSummary($userId);
        $priorityActivities = $this->getPriorityActivities($userId);
        $activityBreakdown = $this->getActivityBreakdown($userId);
        
        return Inertia::render('MyDashboard/MyDashboard', [
            'userRole' => 'pic',
            'activitySummary' => $activitySummary,
            'priorityActivities' => $priorityActivities,
            'activityBreakdown' => $activityBreakdown,
        ]);
    }

    /**
     * Dashboard kosong untuk user yang belum memiliki tugas
     */
    private function getEmptyDashboard()
    {
        return Inertia::render('MyDashboard/MyDashboard', [
            'userRole' => 'empty',
            'message' => 'Anda belum memiliki pekerjaan atau aktivitas yang ditugaskan.',
        ]);
    }

    // ===== METHODS UNTUK LEAD ENGINEER =====

    /**
     * 1. Mengambil data untuk kartu ringkasan beban kerja.
     */
    private function getWorkloadSummary(Builder $query): array
    {
        $activeWorksQuery = (clone $query)->where('project_status', ProjectStatus::IN_PROGRESS);

        return [
            'active_jobs' => (clone $activeWorksQuery)->count(),
            
            'nearing_deadline' => (clone $activeWorksQuery)
                ->whereBetween('executing_target_date', [Carbon::now(), Carbon::now()->addDays(7)])
                ->count(),

            'overdue_jobs' => (clone $activeWorksQuery)
                ->where('executing_target_date', '<', Carbon::now())
                ->count(),
        ];
    }

    /**
     * 2. Mengambil daftar pekerjaan yang paling mendesak.
     */
    private function getPriorityTasks(Builder $query)
    {
        $now = Carbon::now();
        $sevenDaysFromNow = Carbon::now()->addDays(7);

        $tasks = (clone $query)
            ->where('project_status', ProjectStatus::IN_PROGRESS)
            ->where(function ($q) use ($now, $sevenDaysFromNow) {
                $q->where('executing_target_date', '<', $now) // Terlambat
                  ->orWhereBetween('executing_target_date', [$now, $sevenDaysFromNow]); // Mendekati deadline
            })
            ->select('erf_number', 'description', 'executing_target_date')
            ->orderBy('executing_target_date', 'asc')
            ->get()
            ->map(function ($task) use ($now) {
                $targetDate = Carbon::parse($task->executing_target_date);
                $diffHours = $now->diffInHours($targetDate, false);
                $diffDays = round($diffHours / 24);

                if ($diffDays < 0) {
                    $task->urgency_status = 'Terlambat ' . abs($diffDays) . ' hari';
                } else {
                    $task->urgency_status = 'Deadline ' . $diffDays . ' hari lagi';
                }
                return $task;
            });

        return $tasks;
    }
   

    /**
     * 4. Mengambil rincian pekerjaan berdasarkan jenis atau status.
     */
    private function getWorkBreakdown(Builder $query): array
    {
        $byWorkType = (clone $query)
            ->select('work_type', DB::raw('count(*) as total'))
            ->groupBy('work_type')
            ->get()
            ->pluck('total', 'work_type.value');

        $byProjectStatus = (clone $query)
            ->select('project_status', DB::raw('count(*) as total'))
            ->groupBy('project_status')
            ->get()
            ->pluck('total', 'project_status.value');
            
        return [
            'by_work_type' => $byWorkType,
            'by_project_status' => $byProjectStatus,
        ];
    }

    // ===== METHODS UNTUK USER PIC =====

    /**
     * 1. Ringkasan aktivitas untuk user PIC
     */
    private function getActivityPicSummary($userId): array
    {
        // Total aktivitas yang ditangani sebagai PIC
        $totalActivities = ActivityPic::where('user_id', $userId)->count();

        // Aktivitas aktif (berdasarkan end_date yang belum lewat atau null)
        $activeActivities = ActivityPic::where('user_id', $userId)
            ->join('activities', 'activity_pics.activity_id', '=', 'activities.id')
            ->where(function($query) {
                $query->where('activities.end_date', '>=', Carbon::now())
                      ->orWhereNull('activities.end_date');
            })
            ->count();

        // Aktivitas yang mendekati deadline (7 hari ke depan)
        $nearingDeadline = ActivityPic::where('user_id', $userId)
            ->join('activities', 'activity_pics.activity_id', '=', 'activities.id')
            ->whereBetween('activities.end_date', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();

        // Aktivitas yang terlambat (end_date sudah lewat)
        $overdueActivities = ActivityPic::where('user_id', $userId)
            ->join('activities', 'activity_pics.activity_id', '=', 'activities.id')
            ->where('activities.end_date', '<', Carbon::now())
            ->count();

        return [
            'total_activities' => $totalActivities,
            'active_activities' => $activeActivities,
            'nearing_deadline' => $nearingDeadline,
            'overdue_activities' => $overdueActivities,
        ];
    }

    /**
     * 2. Aktivitas prioritas untuk user PIC
     */
    private function getPriorityActivities($userId)
    {
        $now = Carbon::now();
        $sevenDaysFromNow = Carbon::now()->addDays(7);

        $activities = ActivityPic::where('user_id', $userId)
            ->join('activities', 'activity_pics.activity_id', '=', 'activities.id')
            ->join('disciplines', 'activities.discipline_id', '=', 'disciplines.id')
            ->whereNotNull('activities.end_date')
            ->where(function ($q) use ($now, $sevenDaysFromNow) {
                $q->where('activities.end_date', '<', $now) // Terlambat
                  ->orWhereBetween('activities.end_date', [$now, $sevenDaysFromNow]); // Mendekati deadline
            })
            ->select(
                'activities.id',
                'activities.activity_name',
                'activities.activity_description',
                'activities.start_date',
                'activities.end_date',
                'disciplines.name as discipline_name'
            )
            ->orderBy('activities.end_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function ($activity) use ($now) {
                $endDate = Carbon::parse($activity->end_date);
                $diffHours = $now->diffInHours($endDate, false);
                $diffDays = round($diffHours / 24);

                if ($diffDays < 0) {
                    $activity->urgency_status = 'Terlambat ' . abs($diffDays) . ' hari';
                    $activity->urgency_type = 'overdue';
                } else {
                    $activity->urgency_status = 'Deadline ' . $diffDays . ' hari lagi';
                    $activity->urgency_type = 'upcoming';
                }
                return $activity;
            });

        return $activities;
    }

   

    /**
     * 4. Breakdown aktivitas untuk user PIC
     */
    private function getActivityBreakdown($userId): array
    {
        

        // Breakdown berdasarkan jenis pekerjaan (Work Type)
        $byWorkType = ActivityPic::where('user_id', $userId)
            ->join('activities', 'activity_pics.activity_id', '=', 'activities.id')
            ->join('eat', 'activities.eat_id', '=', 'eat.id')
            ->join('works', 'eat.work_id', '=', 'works.id') // Join on works.id instead of erf_number
            ->select('works.work_type', DB::raw('count(DISTINCT activities.id) as total'))
            ->groupBy('works.work_type')
            ->get()
            ->mapWithKeys(function ($item) {
            // Handle potential Enum casting
            $workType = is_object($item->work_type) && method_exists($item->work_type, 'value') 
                ? $item->work_type->value 
                : $item->work_type;
            return [$workType => $item->total];
            });

        // Breakdown berdasarkan status progress (menggunakan progress terbaru)
        $byProgressStatus = ActivityPic::where('user_id', $userId)
            ->join('activities', 'activity_pics.activity_id', '=', 'activities.id')
            ->leftJoin('activity_progress as latest_progress', function($join) {
                $join->on('latest_progress.activity_id', '=', 'activities.id')
                     ->whereRaw('latest_progress.id = (SELECT MAX(id) FROM activity_progress WHERE activity_id = activities.id)');
            })
            ->select(
                DB::raw('CASE 
                    WHEN COALESCE(latest_progress.progress_percentage, 0) >= 100 THEN "Selesai"
                    WHEN COALESCE(latest_progress.progress_percentage, 0) > 0 THEN "Dalam Progress"
                    ELSE "Belum Mulai"
                END as status'),
                DB::raw('count(*) as total')
            )
            ->groupBy(DB::raw('CASE 
                WHEN COALESCE(latest_progress.progress_percentage, 0) >= 100 THEN "Selesai"
                WHEN COALESCE(latest_progress.progress_percentage, 0) > 0 THEN "Dalam Progress"
                ELSE "Belum Mulai"
            END'))
            ->get()
            ->pluck('total', 'status');

        return [
            'by_work_type' => $byWorkType,
            'by_progress_status' => $byProgressStatus,
        ];
    }
}