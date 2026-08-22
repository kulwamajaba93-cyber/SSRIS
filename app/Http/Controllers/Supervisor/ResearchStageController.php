<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ResearchStage;
use App\Models\User;
use Illuminate\Http\Request;

class ResearchStageController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Show the form for updating a student's research stage
     */
    public function edit(User $student)
    {
        $supervisor = auth()->user();
        
        // Verify the student is assigned to this supervisor
        if ($student->supervisor_id !== $supervisor->id) {
            return back()->with('error', 'This student is not assigned to you.');
        }

        $researchStage = $student->researchStage ?? $student->researchStage()->create([
            'stage' => 'initial stage',
            'notes' => 'Research stage initialized'
        ]);

        return view('supervisor.research-stages.edit', compact('student', 'researchStage'));
    }

    /**
     * Update the student's research stage
     */
    public function update(Request $request, User $student)
    {
        $request->validate([
            'stage' => 'required|in:proposal_submitted,under_review,revision,approved,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        $supervisor = auth()->user();
        
        // Verify the student is assigned to this supervisor
        if ($student->supervisor_id !== $supervisor->id) {
            return back()->with('error', 'This student is not assigned to you.');
        }

        $researchStage = $student->researchStage ?? $student->researchStage()->create([
            'stage' => 'initial stage',
            'notes' => 'Research stage initialized'
        ]);

        $researchStage->update([
            'stage' => $request->stage,
            'notes' => $request->notes,
        ]);

        return redirect()->route('supervisor.dashboard')
            ->with('success', 'Research stage updated successfully for ' . $student->name);
    }
}
