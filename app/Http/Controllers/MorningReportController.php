<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MorningReportController extends Controller
{
    public function index()
    {
        // Kumpulan data untuk Laporan Pagi
        $reportData = [
            'new_works' => $this->getNewWorks(),
            'priority_high_works' => $this->getPriorityHighWorks(),
            'finished_works' => $this->getFinishedWorks(),
            'needs_assignment' => $this->getWorksNeedingAssignment(),
            'on_hold_works' => $this->getOnHoldWorks(),
            'nearing_deadline_executing' => $this->getNearingDeadlineExecuting(),
            'executing_phase_works' => $this->getExecutingPhaseWorks(),
            'initiating_planning_works' => $this->getInitiatingPlanningWorks(),
            'nearing_deadline_initiating' => $this->getNearingDeadlineInitiating(),
            'needs_validation' => $this->getWorksNeedingValidation(),
            'needs_eat' => $this->getWorksNeedingEat(),
            'needs_eat_approval' => $this->getWorksNeedingEatApproval(),
        ];

        return Inertia::render('MorningReport/Morning', [
            'reportData' => $reportData,
        ]);
    }

    // 1. ERF YANG MASUK HARI INI & MINGGU INI
    private function getNewWorks(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        $query = Work::with('plant', 'leadEngineer')->orderBy('entry_date', 'desc');

        return [
            'today' => (clone $query)->whereDate('entry_date', $today)->get(),
            'this_week' => (clone $query)->whereBetween('entry_date', [$startOfWeek, $today])->get(),
        ];
    }

    // 2. PEKERJAAN ENGINEERING PRIORITAS HIGH
    private function getPriorityHighWorks()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('work_priority', 'HIGH')
            ->whereNotIn('project_status', ['Finish', 'Cancel'])
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    // 3. PEKERJAAN ENGINEERING YANG SELESAI HARI INI & MINGGU INI
    private function getFinishedWorks(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        $query = Work::with('plant', 'leadEngineer')
            ->where('project_status', 'Finish')
            ->orderBy('executing_actual_date', 'desc');

        return [
            'today' => (clone $query)->whereDate('executing_actual_date', $today)->get(),
            'this_week' => (clone $query)->whereBetween('executing_actual_date', [$startOfWeek, $today])->get(),
        ];
    }

    // 4. PEKERJAAN YANG PERLU PENUNJUKAN LEAD ENGINEERING (TBD)
    private function getWorksNeedingAssignment()
    {
        return Work::with('plant')
            ->whereNull('lead_engineer_id')
            ->whereNotIn('project_status', ['Finish', 'Cancel'])
            ->orderBy('entry_date', 'asc')
            ->get();
    }

    // 5. PEKERJAAN ENGINEERING DENGAN STATUS PENGERJAAN HOLD
    private function getOnHoldWorks()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('project_status', 'On Hold')
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    // 6. PEKERJAAN DENGAN TARGET FASE EXECUTING SELESAI <= 2 MINGGU
    private function getNearingDeadlineExecuting()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('project_status', 'In Progress')
            ->whereBetween('executing_target_date', [Carbon::now(), Carbon::now()->addWeeks(2)])
            ->orderBy('executing_target_date', 'asc')
            ->get();
    }

    // 7. PEKERJAAN ENGINEERING FASE EXECUTING
    private function getExecutingPhaseWorks()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('current_phase', 'Executing')
            ->orderBy('executing_start_date', 'desc')
            ->get();
    }

    // 8. PEKERJAAN ENGINEERING FASE INITIATING DAN PLANNING
    private function getInitiatingPlanningWorks()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereIn('current_phase', ['Initiating', 'Planning'])
            ->orderBy('entry_date', 'desc')
            ->get();
    }
    
    // 9. PEKERJAAN DENGAN TARGET FASE INITIATING/PLANNING SELESAI <= 2 MINGGU
    private function getNearingDeadlineInitiating()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereIn('current_phase', ['Initiating', 'Planning'])
            ->whereBetween('initiating_target_date', [Carbon::now(), Carbon::now()->addWeeks(2)])
            ->orderBy('initiating_target_date', 'asc')
            ->get();
    }

    // 10. PEKERJAAN YANG erf_validated_date NYA MASIH NULL
    private function getWorksNeedingValidation()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereNull('erf_validated_date')
            ->whereNotIn('project_status', ['Finish', 'Cancel', 'Not Started'])
            ->orderBy('entry_date', 'asc')
            ->get();
    }

    // 11. PEKERJAAN YANG BELUM ADA EAT / AKAN DIBUATKAN EAT
    private function getWorksNeedingEat()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereDoesntHave('eat')
            ->whereNotIn('project_status', ['Finish', 'Cancel', 'On Hold'])
            ->orderBy('entry_date', 'asc')
            ->get();
    }
    
    // 12. PEKERJAAN YANG EAT PERLU APPROVAL
    private function getWorksNeedingEatApproval()
    {
        // Asumsi: pekerjaan yang sudah divalidasi dan punya target, tapi belum mulai eksekusi
        return Work::with('plant', 'leadEngineer')
            ->whereNotNull('erf_validated_date')
            ->whereNotNull('initiating_target_date')
            ->whereNull('executing_start_date')
            ->where('project_status', 'In Progress')
            ->orderBy('erf_validated_date', 'asc')
            ->get();
    }
}
