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
        // Validasi input filter
        $validated = $request->validate([
            'filter_type' => 'in:all,yearly,monthly',
            'year' => 'nullable|integer|min:2000|max:' . (Carbon::now()->year + 1),
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $filterType = $validated['filter_type'] ?? 'yearly';
        $currentYear = Carbon::now()->year;
        $selectedYear = $validated['year'] ?? $currentYear;
        $selectedMonth = $validated['month'] ?? Carbon::now()->month;

        // Menghasilkan opsi tahun untuk filter
        $availableYears = range($currentYear, $currentYear - 4);

        // Breadcrumb
        $breadcrumbs = [['name' => 'Dashboard', 'url' => route('dashboard')]];

        // Query dasar berdasarkan filter
        $query = Work::query();
        if ($filterType === 'yearly') {
            $query->whereYear('entry_date', $selectedYear);
        } elseif ($filterType === 'monthly') {
            $query->whereYear('entry_date', $selectedYear)
                  ->whereMonth('entry_date', $selectedMonth);
        }
        // Jika 'all', tidak ada filter waktu yang diterapkan

        // Mengambil nilai dari Enum untuk digunakan kembali
        $allStatuses = array_column(ProjectStatus::cases(), 'value');
        $allWorkTypes = array_column(WorkType::cases(), 'value');
        $allPhases = array_column(CurrentPhase::cases(), 'value');

        // Mengumpulkan data dari fungsi privat
        $statCardsData = $this->getStatCardsData(clone $query, $selectedYear);
        $weeklySummary = $this->getWeeklySummary(clone $query, $allWorkTypes);
        $phaseDetails = $this->getPhaseDetails(clone $query, $allWorkTypes, $allPhases);
        $worksByStatus = $this->getWorkCountsByStatus(clone $query, $allStatuses);
        $worksByType = $this->getWorkCountsByType(clone $query, $allWorkTypes);
        $worksByPlant = $this->getWorkCountsByPlant(clone $query, $allStatuses);
        $worksByTypeAndStatus = $this->getWorkCountsByTypeAndStatus(clone $query, $allWorkTypes, $allStatuses);
        $worksByLeadEngineer = $this->getWorkCountsByLeadEngineer(clone $query, $allStatuses);

        // Data insight baru
        $completionTimeData = $this->getCompletionTimeData(clone $query, $allWorkTypes);
        $onTimeCompletionData = $this->getOnTimeCompletionData(clone $query);
        $engineerWorkload = $this->getEngineerWorkload(clone $query);
        $kpiMonitoringData = $this->getKpiMonitoringData(clone $query, $allWorkTypes);

        return Inertia::render('Dashboard', [
            'breadcrumbs' => $breadcrumbs,
            'header' => 'Dashboard',
            'stats' => [
                'total' => $statCardsData['total'],
                'onProgress' => $statCardsData['onProgress'],
                'overdue' => $statCardsData['overdue'],
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
            'filters' => [
                'type' => $filterType,
                'year' => (int)$selectedYear,
                'month' => (int)$selectedMonth,
                'available_years' => $availableYears,
            ],
            // Data insight baru ditambahkan ke props
            'completionTime' => $completionTimeData,
            'kpiMonitoring' => $kpiMonitoringData,
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
        return [
            'total' => $totalWorks,
            'onProgress' => $onProgressWorks,
            'overdue' => $overdueWorks,
            'closed' => $closedWorks,
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
        // Ambil semua pekerjaan yang sudah selesai dan punya tanggal target & aktual
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
        $lateFinishedWorks = [];

        foreach ($finishedWorks as $work) {
            $targetDate = Carbon::parse($work->executing_target_date);
            $actualDate = Carbon::parse($work->executing_actual_date);

            if ($actualDate->gt($targetDate)) {
                // Actual date > Target date = Terlambat
                $summary['Terlambat']++;
                $lateFinishedWorks[] = [
                    'erf_number' => $work->erf_number,
                    'slug' => $work->slug,
                    'lead_engineer' => $work->leadEngineer?->name,
                    'days_late' => $actualDate->diffInDays($targetDate),
                ];
            } elseif ($actualDate->lt($targetDate)) {
                // Actual date < Target date = Lebih Cepat
                $summary['Lebih Cepat']++;
            } else {
                // Actual date = Target date = Tepat Waktu
                $summary['Tepat Waktu']++;
            }
        }

        // Ambil pekerjaan yang belum selesai tapi sudah lewat target
        $unfinishedLateWorks = (clone $query)
            ->with('leadEngineer:id,name')
            ->whereNotIn('project_status', [ProjectStatus::FINISH, ProjectStatus::CANCEL])
            ->whereNotNull('executing_target_date')
            ->where('executing_target_date', '<', Carbon::now())
            ->get()
            ->map(function ($work) {
                return [
                    'erf_number' => $work->erf_number,
                    'slug' => $work->slug,
                    'lead_engineer' => $work->leadEngineer?->name,
                    'days_late' => round(Carbon::now()->diffInDays(Carbon::parse($work->executing_target_date))),
                ];
            })
            ->sortByDesc('days_late')
            ->values()
            ->all();

        return [
            'summary' => $summary,
            // lateFinishedWorks: pekerjaan selesai tapi terlambat
            'lateFinishedWorks' => collect($lateFinishedWorks)->sortByDesc('days_late')->values()->all(),
            // unfinishedLateWorks: pekerjaan belum selesai tapi sudah lewat target
            'unfinishedLateWorks' => $unfinishedLateWorks,
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
