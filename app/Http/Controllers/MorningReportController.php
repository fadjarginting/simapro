<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MorningReportController extends Controller
{
    public function index()
    {
        // Hanya kirim COUNT untuk initial load - jauh lebih cepat!
        $reportData = [
            'new_works' => [
                'today' => $this->getNewWorksQuery('today')->count(),
                'this_week' => $this->getNewWorksQuery('this_week')->count(),
            ],
            'priority_high_works' => $this->getPriorityHighWorksQuery()->count(),
            'finished_works' => [
                'today' => $this->getFinishedWorksQuery('today')->count(),
                'this_week' => $this->getFinishedWorksQuery('this_week')->count(),
            ],
            'needs_assignment' => $this->getWorksNeedingAssignmentQuery()->count(),
            'on_hold_works' => $this->getOnHoldWorksQuery()->count(),
            'nearing_deadline_executing' => $this->getNearingDeadlineExecutingQuery()->count(),
            'executing_phase_works' => $this->getExecutingPhaseWorksQuery()->count(),
            'initiating_planning_works' => $this->getInitiatingPlanningWorksQuery()->count(),
            'nearing_deadline_initiating' => $this->getNearingDeadlineInitiatingQuery()->count(),
            'needs_validation' => $this->getWorksNeedingValidationQuery()->count(),
            'needs_eat' => $this->getWorksNeedingEatQuery()->count(),
            'needs_eat_approval' => $this->getWorksNeedingEatApprovalQuery()->count(),
        ];

        return Inertia::render('MorningReport/Morning', [
            'reportData' => $reportData,
        ]);
    }

    // API endpoint untuk load data detail on-demand
    public function loadCardData(Request $request, string $cardType)
    {
        $period = $request->get('period'); // untuk card yang punya today/this_week
        
        $data = match($cardType) {
            'new_works' => $this->getNewWorksQuery($period)->get(),
            'priority_high_works' => $this->getPriorityHighWorksQuery()->get(),
            'finished_works' => $this->getFinishedWorksQuery($period)->get(),
            'needs_assignment' => $this->getWorksNeedingAssignmentQuery()->get(),
            'on_hold_works' => $this->getOnHoldWorksQuery()->get(),
            'nearing_deadline_executing' => $this->getNearingDeadlineExecutingQuery()->get(),
            'executing_phase_works' => $this->getExecutingPhaseWorksQuery()->get(),
            'initiating_planning_works' => $this->getInitiatingPlanningWorksQuery()->get(),
            'nearing_deadline_initiating' => $this->getNearingDeadlineInitiatingQuery()->get(),
            'needs_validation' => $this->getWorksNeedingValidationQuery()->get(),
            'needs_eat' => $this->getWorksNeedingEatQuery()->get(),
            'needs_eat_approval' => $this->getWorksNeedingEatApprovalQuery()->get(),
            default => [],
        };

        return response()->json($data);
    }

    // QUERY BUILDERS - Return query builder untuk reusability
    
    private function getNewWorksQuery(string $period = 'today')
    {
        $query = Work::with('plant', 'leadEngineer')->orderBy('entry_date', 'desc');
        
        if ($period === 'today') {
            return $query->whereDate('entry_date', Carbon::today());
        }
        
        return $query->whereBetween('entry_date', [
            Carbon::now()->startOfWeek(), 
            Carbon::today()
        ]);
    }

    private function getPriorityHighWorksQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('work_priority', 'HIGH')
            ->whereNotIn('project_status', ['Finish', 'Cancel'])
            ->orderBy('entry_date', 'desc');
    }

    private function getFinishedWorksQuery(string $period = 'today')
    {
        $query = Work::with('plant', 'leadEngineer')
            ->where('project_status', 'Finish')
            ->orderBy('executing_actual_date', 'desc');
        
        if ($period === 'today') {
            return $query->whereDate('executing_actual_date', Carbon::today());
        }
        
        return $query->whereBetween('executing_actual_date', [
            Carbon::now()->startOfWeek(),
            Carbon::today()
        ]);
    }

    private function getWorksNeedingAssignmentQuery()
    {
        return Work::with('plant')
            ->whereNull('lead_engineer_id')
            ->whereNotIn('project_status', ['Finish', 'Cancel'])
            ->orderBy('entry_date', 'asc');
    }

    private function getOnHoldWorksQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('project_status', 'On Hold')
            ->orderBy('entry_date', 'desc');
    }

    private function getNearingDeadlineExecutingQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('project_status', 'In Progress')
            ->whereBetween('executing_target_date', [
                Carbon::now(), 
                Carbon::now()->addWeeks(2)
            ])
            ->orderBy('executing_target_date', 'asc');
    }

    private function getExecutingPhaseWorksQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->where('current_phase', 'Executing')
            ->orderBy('executing_start_date', 'desc');
    }

    private function getInitiatingPlanningWorksQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereIn('current_phase', ['Initiating', 'Planning'])
            ->orderBy('entry_date', 'desc');
    }
    
    private function getNearingDeadlineInitiatingQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereIn('current_phase', ['Initiating', 'Planning'])
            ->whereBetween('initiating_target_date', [
                Carbon::now(), 
                Carbon::now()->addWeeks(2)
            ])
            ->orderBy('initiating_target_date', 'asc');
    }

    private function getWorksNeedingValidationQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereNull('erf_validated_date')
            ->whereNotIn('project_status', ['Finish', 'Cancel', 'Not Started'])
            ->orderBy('entry_date', 'asc');
    }

    private function getWorksNeedingEatQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereDoesntHave('eat')
            ->whereNotIn('project_status', ['Finish', 'Cancel', 'On Hold'])
            ->orderBy('entry_date', 'asc');
    }
    
    private function getWorksNeedingEatApprovalQuery()
    {
        return Work::with('plant', 'leadEngineer')
            ->whereNotNull('erf_validated_date')
            ->whereNotNull('initiating_target_date')
            ->whereNull('executing_start_date')
            ->where('project_status', 'In Progress')
            ->orderBy('erf_validated_date', 'asc');
    }
}