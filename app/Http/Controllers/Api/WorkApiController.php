<?php

namespace App\Http\Controllers\Api;

use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class WorkApiController extends Controller
{
    

    /**
     * Store atau assign Lead Engineer to a Work.
     */
    public function assignLeadEngineer(Request $request, int $workId): JsonResponse
    {
        $validated = $request->validate([
            'lead_engineer_id' => 'required|exists:users,id',
        ]);

        // Find the work by ID
        $work = Work::findOrFail($workId);

        // Assign the lead engineer
        $work->lead_engineer_id = $validated['lead_engineer_id'];
        $work->save();

        return response()->json([
            'message' => 'Lead Engineer assigned successfully.',
            'work' => $work,
        ], 200);
    }
}