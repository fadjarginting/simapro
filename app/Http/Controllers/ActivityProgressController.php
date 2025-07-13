<?php
namespace App\Http\Controllers;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\ActivityProgress;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ActivityProgressController extends Controller
{
    public function __construct()
    {
        // Middleware to ensure user is authenticated
        $this->middleware('auth');
    }

    // addProgress method to handle adding progress to an activity
    public function addProgress(Request $request, Activity $activity)
    {
        $request->validate([
            'progress_description' => 'required|string|max:255',
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        // Validasi kalau user ini adalah PIC
        if (!$activity->pics()->where('user_id', Auth::user()->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to add progress to this activity.'
            ], 403);
        }

        // Validasi bahwa progress berikutnya tidak boleh kurang dari progress terakhir
        $lastProgress = $activity->progress()->latest()->first();
        if ($lastProgress && $request->input('progress_percentage') < $lastProgress->progress_percentage) {
            return response()->json([
                'success' => false,
                'message' => 'Persentase progress tidak boleh kurang dari progress terakhir yang tercatat.'
            ], 422);
        }

        $progress = $activity->progress()->create([
            'progress_description' => $request->input('progress_description'),
            'progress_percentage' => $request->input('progress_percentage'),
            'reported_by' => Auth::id(),
        ]);

        // Return JSON response instead of redirect
        return response()->json([
            'success' => true,
            'message' => 'Progress added successfully.',
            'progress' => $progress,
        ], 200);
    }
}