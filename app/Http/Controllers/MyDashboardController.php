<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\WorkType;
use App\Models\Work;
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
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            // Jika pengguna tidak login, arahkan ke halaman login
            return redirect()->route('login');
        }

        // Query dasar yang sudah difilter untuk pengguna ini
        $baseQuery = Work::where('lead_engineer_id', $userId);

        // Mengumpulkan semua data dari metode privat
        $workloadSummary = $this->getWorkloadSummary(clone $baseQuery);
        $priorityTasks = $this->getPriorityTasks(clone $baseQuery);
        $performanceSnapshot = $this->getPerformanceSnapshot(clone $baseQuery);
        $workBreakdown = $this->getWorkBreakdown(clone $baseQuery);

        return Inertia::render('MyDashboard/MyDashboard', [
            'workloadSummary' => $workloadSummary,
            'priorityTasks' => $priorityTasks,
            'performanceSnapshot' => $performanceSnapshot,
            'workBreakdown' => $workBreakdown,
        ]);
    }

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
     * 3. Mengambil data untuk analisis performa pribadi.
     */
    private function getPerformanceSnapshot(Builder $query): array
    {
        $finishedWorks = (clone $query)
            ->where('project_status', ProjectStatus::FINISH)
            ->whereNotNull('entry_date')
            ->whereNotNull('executing_actual_date')
            ->whereNotNull('executing_target_date')
            ->select('entry_date', 'executing_actual_date', 'executing_target_date')
            ->get();

        if ($finishedWorks->isEmpty()) {
            return [
                'on_time_delivery' => ['Tepat Waktu' => 0, 'Terlambat' => 0, 'Lebih Cepat' => 0],
                'average_completion_time' => 0,
            ];
        }

        // Analisis Ketepatan Waktu
        $onTimeDelivery = ['Tepat Waktu' => 0, 'Terlambat' => 0, 'Lebih Cepat' => 0];
        foreach ($finishedWorks as $work) {
            $diff = Carbon::parse($work->executing_actual_date)->diffInDays(Carbon::parse($work->executing_target_date), false);
            if ($diff < 0) {
                $onTimeDelivery['Terlambat']++;
            } elseif ($diff > 0) {
                $onTimeDelivery['Lebih Cepat']++;
            } else {
                $onTimeDelivery['Tepat Waktu']++;
            }
        }

        // Rata-rata Waktu Penyelesaian
        $totalCompletionDays = $finishedWorks->sum(function ($work) {
            return Carbon::parse($work->executing_actual_date)->diffInDays(Carbon::parse($work->entry_date));
        });
        $averageCompletionTime = round($totalCompletionDays / $finishedWorks->count());

        return [
            'on_time_delivery' => $onTimeDelivery,
            'average_completion_time' => $averageCompletionTime,
        ];
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
}
