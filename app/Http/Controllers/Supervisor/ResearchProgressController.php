<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ResearchStage;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Http\Request;

class ResearchProgressController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes
    }

    /**
     * Display the supervisor's view of student progress
     */
    public function index()
    {
        $supervisor = auth()->user();
        $students = $supervisor->assignedStudents()->with(['studentProgress' => function ($q) {
            $q->with('stage');
        }])->get();

        return view('supervisor.research-progress.index', compact('students'));
    }

    /**
     * Show the form for approving a student's stage
     */
    public function edit(User $student)
    {
        $supervisor = auth()->user();
        
        // Verify the student is assigned to this supervisor
        if ($student->supervisor_id !== $supervisor->id) {
            return back()->with('error', 'This student is not assigned to you.');
        }

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
                        'approved_by' => $supervisor->id,
                        'approved_at' => now(),
                    ]);
                    $progress[$completedStage->id] = $progressItem->fresh();
                }
            }
        }

        return view('supervisor.research-progress.edit', compact('student', 'stages', 'progress'));
    }

    /**
     * Approve a student's current stage and advance to next
     */
    public function update(Request $request, User $student, ResearchStage $stage)
    {
        $supervisor = auth()->user();
        
        // Verify the student is assigned to this supervisor
        if ($student->supervisor_id !== $supervisor->id) {
            return back()->with('error', 'This student is not assigned to you.');
        }

        $currentProgress = StudentProgress::where('student_id', $student->id)
            ->where('stage_id', $stage->id)
            ->first();

        if (!$currentProgress || $currentProgress->status !== 'pending') {
            return back()->with('error', 'This stage is not available for approval.');
        }

        // Mark current stage as approved
        $currentProgress->update([
            'status' => 'approved',
            'approved_by' => $supervisor->id,
            'approved_at' => now(),
        ]);

        // Log interaction for stage approval
        \App\Models\Interaction::create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'student_progress_id' => $currentProgress->id,
            'action_type' => \App\Models\Interaction::ACTION_APPROVAL,
            'notes' => 'Approved stage: ' . $stage->name,
            'status' => \App\Models\Interaction::STATUS_COMPLETED,
            'document_reference' => 'Stage ' . $stage->step_number,
        ]);

        // Find and unlock the next stage
        $nextStage = ResearchStage::where('step_number', $stage->step_number + 1)->first();
        if ($nextStage) {
            StudentProgress::updateOrCreate(
                ['student_id' => $student->id, 'stage_id' => $nextStage->id],
                ['status' => 'pending']
            );
        }

        return back()->with('success', 'Stage approved successfully!');
    }
}
