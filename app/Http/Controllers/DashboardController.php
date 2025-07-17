<?php

namespace App\Http\Controllers;

use App\Enums\CurrentPhase;
use App\Enums\ProjectStatus;
use App\Enums\WorkType;
use App\Models\Plant;
use App\Models\User;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Menghasilkan opsi tahun untuk filter
        $currentYear = Carbon::now()->year;
        $availableYears = range($currentYear, $currentYear - 4);
        $selectedYear = $request->input('year', $currentYear);

        // Breadcrumb
        $breadcrumbs = [['name' => 'Dashboard', 'url' => route('dashboard')]];

        // Query dasar untuk tahun yang dipilih
        $queryForYear = Work::whereYear('entry_date', $selectedYear);

        // Mengambil nilai dari Enum untuk digunakan kembali
        $allStatuses = array_column(ProjectStatus::cases(), 'value');
        $allWorkTypes = array_column(WorkType::cases(), 'value');
        $allPhases = array_column(CurrentPhase::cases(), 'value');

        // Mengumpulkan data dari fungsi privat
        $statCardsData = $this->getStatCardsData(clone $queryForYear, $selectedYear);
        $weeklySummary = $this->getWeeklySummary(clone $queryForYear, $allWorkTypes);
        $phaseDetails = $this->getPhaseDetails(clone $queryForYear, $allWorkTypes, $allPhases);
        $worksByStatus = $this->getWorkCountsByStatus(clone $queryForYear, $allStatuses);
        $worksByType = $this->getWorkCountsByType(clone $queryForYear, $allWorkTypes);
        $worksByPlant = $this->getWorkCountsByPlant(clone $queryForYear, $allStatuses);
        $worksByTypeAndStatus = $this->getWorkCountsByTypeAndStatus(clone $queryForYear, $allWorkTypes, $allStatuses);
        $worksByLeadEngineer = $this->getWorkCountsByLeadEngineer(clone $queryForYear, $allStatuses);

        // Data insight baru
        $completionTimeData = $this->getCompletionTimeData(clone $queryForYear, $allWorkTypes);
        $onTimeCompletionData = $this->getOnTimeCompletionData(clone $queryForYear);
        $engineerWorkload = $this->getEngineerWorkload(clone $queryForYear);
        $kpiMonitoringData = $this->getKpiMonitoringData(clone $queryForYear, $allWorkTypes);

        return Inertia::render('Dashboard', [
            'breadcrumbs' => $breadcrumbs,
            'header' => 'Dashboard',
            'stats' => [
                'total' => $statCardsData['total'],
                'onProgress' => $statCardsData['onProgress'],
                'overdue' => $statCardsData['overdue'],
                'totalWorkChange' => $statCardsData['totalWorkChange'],
                'onProgressWorkChange' => $statCardsData['onProgressWorkChange'],
                'overdueWorkChange' => $statCardsData['overdueWorkChange'],
                'closed' => $statCardsData['closed'],
                'byStatus' => $worksByStatus,
                'byType' => $worksByType,
                'byPlant' => $worksByPlant,
                'byTypeAndStatus' => $worksByTypeAndStatus,
                'byLeadEngineer' => $worksByLeadEngineer,
            ],
            'allStatuses' => $allStatuses,
            'weeklySummary' => $weeklySummary,
            'phaseDetails' => $phaseDetails,
            'availableYears' => $availableYears,
            'selectedYear' => (int)$selectedYear,
            // Data insight baru ditambahkan ke props
            'completionTime' => $completionTimeData,
            'kpiMonitoring' => $kpiMonitoringData, // Data baru ditambahkan di sini
            'onTimeCompletion' => $onTimeCompletionData,
            'engineerWorkload' => $engineerWorkload,
        ]);
    }

    /**
     * Mengambil data untuk kartu statistik utama.
     */
    private function getStatCardsData(Builder $query, $selectedYear)
    {
        $totalWorks = (clone $query)->count();
        $onProgressWorks = (clone $query)->where('project_status', ProjectStatus::IN_PROGRESS)->count();
        $overdueWorks = (clone $query)
            ->where('executing_target_date', '<', Carbon::now())
            ->whereNotIn('project_status', [ProjectStatus::FINISH, ProjectStatus::CANCEL])
            ->count();
        $closedWorks = (clone $query)->where('project_status', ProjectStatus::FINISH)->count();

        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();
        $totalWorksLastWeek = Work::whereYear('entry_date', $selectedYear)
            ->whereBetween('entry_date', [$startOfLastWeek, $endOfLastWeek])
            ->count();

        $calculateChange = fn($current, $previous) => $previous == 0 ? ($current > 0 ? 100.0 : 0.0) : (($current - $previous) / $previous) * 100;

        return [
            'total' => $totalWorks,
            'onProgress' => $onProgressWorks,
            'overdue' => $overdueWorks,
            'closed' => $closedWorks,
            'totalWorkChange' => round($calculateChange($totalWorks, $totalWorksLastWeek), 1),
            'onProgressWorkChange' => round($calculateChange($onProgressWorks, $totalWorksLastWeek), 1),
            'overdueWorkChange' => round($calculateChange($overdueWorks, $totalWorksLastWeek), 1),
        ];
    }

    /**
     * Mengambil data ringkasan pekerjaan minggu ini.
     */
    private function getWeeklySummary(Builder $query, array $allWorkTypes)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $summaryByType = [];
        foreach ($allWorkTypes as $workType) {
            $baseQuery = (clone $query)->where('work_type', $workType);
            $summaryByType[$workType] = [
                'masuk' => (clone $baseQuery)->whereBetween('entry_date', [$startOfWeek, $endOfWeek])->count(),
                'selesai' => (clone $baseQuery)->where('project_status', ProjectStatus::FINISH)->whereBetween('executing_actual_date', [$startOfWeek, $endOfWeek])->count(),
                'initiating' => (clone $baseQuery)->where('current_phase', 'Initiating')->count(),
                'executing' => (clone $baseQuery)->where('current_phase', 'Executing')->count(),
            ];
        }

        return [
            'startOfWeek' => $startOfWeek->toDateString(),
            'endOfWeek' => $endOfWeek->toDateString(),
            'summary' => $summaryByType,
        ];
    }

    /**
     * Mengambil detail status pekerjaan per fase.
     */
    private function getPhaseDetails(Builder $query, array $allWorkTypes, array $allPhases)
    {
        $phaseDetailsQuery = (clone $query)
            ->select('work_type', 'current_phase', DB::raw('count(*) as total'))
            ->whereIn('current_phase', $allPhases)
            ->groupBy('work_type', 'current_phase')
            ->get();

        return collect($allWorkTypes)->mapWithKeys(function ($workType) use ($query, $phaseDetailsQuery, $allPhases) {
            $dataForType = $phaseDetailsQuery->where('work_type.value', $workType);
            $totalForWorkType = (clone $query)->where('work_type', $workType)->count();
            $phaseCounts = collect($allPhases)->mapWithKeys(function ($phase) use ($dataForType) {
                return [$phase => $dataForType->firstWhere('current_phase', $phase)->total ?? 0];
            });
            return [$workType => ['total' => $totalForWorkType, 'phases' => $phaseCounts]];
        });
    }

    /**
     * Mengelompokkan pekerjaan berdasarkan status.
     */
    private function getWorkCountsByStatus(Builder $query, array $allStatuses)
    {
        $workCounts = (clone $query)
            ->select('project_status', DB::raw('count(*) as total'))
            ->groupBy('project_status')
            ->get()
            ->pluck('total', 'project_status.value');

        return collect($allStatuses)->mapWithKeys(function ($status) use ($workCounts) {
            return [$status => $workCounts->get($status, 0)];
        });
    }

    /**
     * Mengelompokkan pekerjaan berdasarkan jenis.
     */
    private function getWorkCountsByType(Builder $query, array $allWorkTypes)
    {
        $workCounts = (clone $query)
            ->select('work_type', DB::raw('count(*) as total'))
            ->groupBy('work_type')
            ->get()
            ->pluck('total', 'work_type.value');

        return collect($allWorkTypes)->mapWithKeys(function ($type) use ($workCounts) {
            return [$type => $workCounts->get($type, 0)];
        });
    }

    /**
     * Mengelompokkan pekerjaan berdasarkan plant dan status.
     */
    private function getWorkCountsByPlant(Builder $query, array $allStatuses)
    {
        $allPlants = Plant::orderBy('name')->get();
        $workCountsByPlant = (clone $query)
            ->whereNotNull('plant_id')
            ->select('plant_id', 'project_status', DB::raw('count(*) as total'))
            ->groupBy('plant_id', 'project_status')
            ->get()
            ->groupBy('plant_id');

        return $allPlants->mapWithKeys(function ($plant) use ($workCountsByPlant, $allStatuses) {
            $countsForPlant = $workCountsByPlant->get($plant->id);
            $statusCounts = collect($allStatuses)->mapWithKeys(function ($status) use ($countsForPlant) {
                return [$status => $countsForPlant?->firstWhere('project_status.value', $status)->total ?? 0];
            });
            return [$plant->name => $statusCounts];
        });
    }

    /**
     * Mengelompokkan pekerjaan berdasarkan jenis dan status.
     */
    private function getWorkCountsByTypeAndStatus(Builder $query, array $allWorkTypes, array $allStatuses)
    {
        $workCounts = (clone $query)
            ->select('work_type', 'project_status', DB::raw('count(*) as total'))
            ->groupBy('work_type', 'project_status')
            ->get()
            ->groupBy('work_type.value');

        return collect($allWorkTypes)->mapWithKeys(function ($workType) use ($workCounts, $allStatuses) {
            $countsForType = $workCounts->get($workType);
            $statusCounts = collect($allStatuses)->mapWithKeys(function ($status) use ($countsForType) {
                return [$status => $countsForType?->firstWhere('project_status.value', $status)->total ?? 0];
            });
            return [$workType => $statusCounts];
        });
    }

    /**
     * Mengelompokkan pekerjaan berdasarkan lead engineer dan status.
     */
    private function getWorkCountsByLeadEngineer(Builder $query, array $allStatuses)
    {
        $leadEngineerIds = (clone $query)->whereNotNull('lead_engineer_id')->distinct()->pluck('lead_engineer_id');
        $allLeadEngineers = User::whereIn('id', $leadEngineerIds)->orderBy('name')->get(['id', 'name']);
        $workCounts = (clone $query)
            ->whereNotNull('lead_engineer_id')
            ->select('lead_engineer_id', 'project_status', DB::raw('count(*) as total'))
            ->groupBy('lead_engineer_id', 'project_status')
            ->get()
            ->groupBy('lead_engineer_id');

        return $allLeadEngineers->mapWithKeys(function ($engineer) use ($workCounts, $allStatuses) {
            $countsForEngineer = $workCounts->get($engineer->id);
            $statusCounts = collect($allStatuses)->mapWithKeys(function ($status) use ($countsForEngineer) {
                return [$status => $countsForEngineer?->firstWhere('project_status.value', $status)->total ?? 0];
            });
            return [$engineer->name => $statusCounts];
        });
    }

    /**
     * Menghitung rata-rata waktu penyelesaian pekerjaan.
     */
    private function getCompletionTimeData(Builder $query, array $allWorkTypes)
    {
        $finishedWorksQuery = (clone $query)
            ->where('project_status', ProjectStatus::FINISH)
            ->whereNotNull('entry_date')
            ->whereNotNull('executing_actual_date');

        $averageOverall = (clone $finishedWorksQuery)->avg(DB::raw('DATEDIFF(executing_actual_date, entry_date)'));

        $byWorkTypeRaw = (clone $finishedWorksQuery)
            ->select('work_type', DB::raw('AVG(DATEDIFF(executing_actual_date, entry_date)) as avg_days'))
            ->groupBy('work_type')
            ->get()
            ->pluck('avg_days', 'work_type.value');

        $byWorkType = collect($allWorkTypes)->mapWithKeys(function ($type) use ($byWorkTypeRaw) {
            return [$type => round($byWorkTypeRaw->get($type, 0))];
        });

        $byPlant = (clone $finishedWorksQuery)
            ->join('plants', 'works.plant_id', '=', 'plants.id')
            ->select('plants.name as plant_name', DB::raw('AVG(DATEDIFF(executing_actual_date, entry_date)) as avg_days'))
            ->groupBy('plants.name')
            ->orderBy('plants.name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->plant_name => round($item->avg_days)];
            });

        return [
            'averageOverall' => round($averageOverall ?? 0),
            'byWorkType' => $byWorkType,
            'byPlant' => $byPlant,
        ];
    }

    /**
     * Menganalisis ketepatan waktu penyelesaian pekerjaan.
     */
    private function getOnTimeCompletionData(Builder $query)
    {
        $finishedWorks = (clone $query)
            ->with('leadEngineer:id,name')
            ->where('project_status', ProjectStatus::FINISH)
            ->whereNotNull('executing_target_date')
            ->whereNotNull('executing_actual_date')
            ->get();

        $summary = [
            'Tepat Waktu' => 0,
            'Terlambat' => 0,
            'Lebih Cepat' => 0,
        ];
        $lateWorks = [];

        foreach ($finishedWorks as $work) {
            $targetDate = Carbon::parse($work->executing_target_date);
            $actualDate = Carbon::parse($work->executing_actual_date);
            
            // Hitung selisih hari: positif jika terlambat, negatif jika lebih cepat
            $diffDays = $actualDate->diffInDays($targetDate, false);

            if ($diffDays < 0) {
                // Actual date > Target date = Terlambat
                $summary['Terlambat']++;
                $lateWorks[] = [
                    'erf_number' => $work->erf_number,
                    'slug' => $work->slug,
                    'lead_engineer' => $work->leadEngineer?->name,
                    'days_late' => abs($diffDays),
                ];
            } elseif ($diffDays > 0) {
                // Actual date < Target date = Lebih Cepat
                $summary['Lebih Cepat']++;
            } else {
                // Actual date = Target date = Tepat Waktu
                $summary['Tepat Waktu']++;
            }
        }

        return [
            'summary' => $summary,
            'lateWorks' => collect($lateWorks)->sortByDesc('days_late')->values()->all(),
        ];
    }

    /**
     * Menghitung beban kerja aktif per engineer.
     */
    private function getEngineerWorkload(Builder $query)
    {
        return (clone $query)
            ->where('project_status', ProjectStatus::IN_PROGRESS)
            ->whereNotNull('lead_engineer_id')
            ->join('users', 'works.lead_engineer_id', '=', 'users.id')
            ->select('users.name as engineer_name', DB::raw('count(works.id) as total'))
            ->groupBy('users.name')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'engineer_name');
    }

    /**
     * Memantau KPI penyelesaian pekerjaan per semester aktif.
     */
    private function getKpiMonitoringData(Builder $query, array $allWorkTypes)
    {
        $now = Carbon::now();
        // Menentukan semester aktif berdasarkan bulan saat ini
        if ($now->month <= 6) {
            // Semester 1: 1 Januari - 30 Juni
            $semesterStartMonth = 1;
            $semesterEndMonth = 6;
        } else {
            // Semester 2: 1 Juli - 31 Desember
            $semesterStartMonth = 7;
            $semesterEndMonth = 12;
        }

        // Ambil semua data yang relevan dalam satu query untuk efisiensi
        // Terapkan filter semester ke klon dari query yang ada
        $kpiDataRaw = (clone $query)
            ->where(function ($q) use ($semesterStartMonth, $semesterEndMonth) {
                $q->whereMonth('entry_date', '>=', $semesterStartMonth)
                  ->whereMonth('entry_date', '<=', $semesterEndMonth);
            })
            ->select('work_type', 'project_status')
            ->get();

        // Untuk mendapatkan periode start/end yang tepat, kita perlu tahun.
        // Kita bisa mengambilnya dari record pertama jika ada.
        $firstWork = (clone $query)->first();
        $year = $firstWork ? Carbon::parse($firstWork->entry_date)->year : $now->year;

        $semesterStart = Carbon::create($year, $semesterStartMonth, 1)->startOfDay();
        $semesterEnd = Carbon::create($year, $semesterEndMonth)->endOfMonth()->endOfDay();

        $results = [];
        foreach ($allWorkTypes as $workType) {
            $worksForType = $kpiDataRaw->where('work_type.value', $workType);

            $total = $worksForType->count();
            $finished = $worksForType->where('project_status', ProjectStatus::FINISH)->count();

            $percentage = ($total > 0) ? ($finished / $total) * 100 : 0;

            $results[$workType] = [
                'percentage' => round($percentage, 2),
                'period_start' => $semesterStart->isoFormat('DD MMMM YYYY'),
                'period_end' => $semesterEnd->isoFormat('DD MMMM YYYY'),
            ];
        }

        return [
            'period_start' => $semesterStart->isoFormat('DD MMMM YYYY'),
            'period_end' => $semesterEnd->isoFormat('DD MMMM YYYY'),
            'data' => $results,
            'currentSemester' => $now->month <= 6 ? 1 : 2,
        ];
    }
}
