<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ResearchStage;
use App\Models\StudentProgress;
use Illuminate\Http\Request;

class ResearchProgressController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display the student's research progress
     */
    public function index()
    {
        $student = auth()->user();
        $stages = ResearchStage::orderBy('step_number')->get();
        
        // Initialize progress for student if not exists
        foreach ($stages as $stage) {
            StudentProgress::firstOrCreate(
                ['student_id' => $student->id, 'stage_id' => $stage->id],
                ['status' => $stage->step_number == 1 ? 'pending' : 'locked']
            );
        }

        $progress = StudentProgress::where('student_id', $student->id)
            ->with('stage')
            ->get()
            ->keyBy('stage_id');

        // Check if student has an approved report to mark the "Completed" step
        $completedStage = $stages->where('step_number', 5)->first();
        if ($completedStage) {
            $hasApprovedReport = $student->proposals()
                ->where('document_type', 'report')
                ->where('status', 'approved')
                ->exists();
            
            if ($hasApprovedReport) {
                $progressItem = $progress[$completedStage->id] ?? null;
                if ($progressItem && $progressItem->status !== 'approved') {
                    $progressItem->update([
                        'status' => 'approved',
                        'approved_by' => $student->supervisor_id,
                        'approved_at' => now(),
                    ]);
                    $progress[$completedStage->id] = $progressItem->fresh();
                }
            }
        }

        return view('student.research-progress.index', compact('stages', 'progress'));
    }
}
