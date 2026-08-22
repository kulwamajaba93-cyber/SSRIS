<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Proposal;
use App\Services\SmsService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Show the form for creating new feedback
     */
    public function create(Proposal $proposal)
    {
        $supervisor = auth()->user();
        
        // Verify the proposal belongs to a student assigned to this supervisor
        if ($proposal->student->supervisor_id !== $supervisor->id) {
            abort(403, 'This proposal is not from your assigned student.');
        }
        
        return view('supervisor.feedback.create', compact('proposal'));
    }

    /**
     * Store a newly created feedback
     */
    public function store(Request $request, Proposal $proposal, SmsService $smsService)
    {
        $supervisor = auth()->user();
        
        // Verify the proposal belongs to a student assigned to this supervisor
        if ($proposal->student->supervisor_id !== $supervisor->id) {
            abort(403, 'This proposal is not from your assigned student.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'comments' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'action_required' => 'nullable|string',
            'due_date' => 'nullable|date',
            'proposal_status' => 'nullable|in:pending,under_review,reviewed,approved,rejected,revision',
        ]);

        Feedback::create([
            'project_id' => $proposal->project_id,
            'proposal_id' => $proposal->id,
            'student_id' => $proposal->student_id,
            'supervisor_id' => auth()->id(),
            'title' => $request->title,
            'comments' => $request->comments,
            'priority' => $request->priority,
            'action_required' => $request->action_required,
            'due_date' => $request->due_date,
            'type' => 'proposal_feedback',
            'status' => 'pending',
        ]);

        // Update proposal status if provided
        if ($request->has('proposal_status') && $request->proposal_status) {
            $proposal->update([
                'status' => $request->proposal_status,
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            // If proposal status is approved, handle research stage progression
            if ($request->proposal_status === 'approved') {
                // Get the step number for this document type
                $stepNumber = Proposal::getStepNumberForDocumentType($proposal->document_type);

                if ($stepNumber) {
                    // Get the corresponding research stage
                    $stage = \App\Models\ResearchStage::where('step_number', $stepNumber)->first();
                    if ($stage) {
                        // Mark the current stage as approved
                        \App\Models\StudentProgress::updateOrCreate(
                            ['student_id' => $proposal->student_id, 'stage_id' => $stage->id],
                            [
                                'status' => 'approved',
                                'approved_by' => $supervisor->id,
                                'approved_at' => now(),
                            ]
                        );

                        // Handle final stage: if document is report (step 4), mark step 5 (Completed) as approved
                        if ($proposal->document_type === 'report') {
                            $completedStage = \App\Models\ResearchStage::where('step_number', 5)->first();
                            if ($completedStage) {
                                \App\Models\StudentProgress::updateOrCreate(
                                    ['student_id' => $proposal->student_id, 'stage_id' => $completedStage->id],
                                    [
                                        'status' => 'approved',
                                        'approved_by' => $supervisor->id,
                                        'approved_at' => now(),
                                    ]
                                );
                            }
                        } else {
                            // Unlock the next stage if there is one
                            $nextStage = \App\Models\ResearchStage::where('step_number', $stepNumber + 1)->first();
                            if ($nextStage) {
                                \App\Models\StudentProgress::updateOrCreate(
                                    ['student_id' => $proposal->student_id, 'stage_id' => $nextStage->id],
                                    ['status' => 'pending']
                                );
                            }
                        }
                    }
                }
            }

            // Send SMS notification based on proposal status
            try {
                $documentTypeName = ucfirst(str_replace('_', ' ', $proposal->document_type));

                if ($request->proposal_status === 'approved') {
                    $smsService->sendDocumentApprovalNotification($proposal->student, $documentTypeName, $proposal->id);
                } elseif ($request->proposal_status === 'rejected') {
                    $smsService->sendDocumentRejectionNotification($proposal->student, $documentTypeName, $proposal->id);
                }
            } catch (\Exception $e) {
                // Log error but don't fail the feedback submission
                \Log::error('SMS notification failed', ['error' => $e->getMessage()]);
            }
        }
        // Update proposal status if priority is high or urgent and no status was provided
        elseif ($request->priority === 'high' || $request->priority === 'urgent') {
            $proposal->update([
                'status' => 'revision',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);
        }

        // Send SMS notification to student about new feedback
        try {
            $smsService->sendFeedbackNotification($proposal->student, $proposal->title, $proposal->id);
        } catch (\Exception $e) {
            // Log error but don't fail the feedback submission
            \Log::error('SMS notification failed', ['error' => $e->getMessage()]);
        }

        // Log interaction for feedback
        $interactionStatus = \App\Models\Interaction::STATUS_REVISION_REQUIRED;
        $actionType = \App\Models\Interaction::ACTION_FEEDBACK;
        if ($request->proposal_status === 'approved') {
            $interactionStatus = \App\Models\Interaction::STATUS_COMPLETED;
            $actionType = \App\Models\Interaction::ACTION_APPROVAL;
        }

        \App\Models\Interaction::create([
            'student_id' => $proposal->student_id,
            'supervisor_id' => $supervisor->id,
            'proposal_id' => $proposal->id,
            'feedback_id' => Feedback::latest()->first()->id,
            'action_type' => $actionType,
            'notes' => $request->comments,
            'status' => $interactionStatus,
            'document_reference' => ucfirst(str_replace('_', ' ', $proposal->document_type)) . ' v' . $proposal->version,
        ]);

        return redirect()->route('supervisor.dashboard')
            ->with('success', 'Feedback submitted successfully');
    }

    /**
     * Display the specified feedback
     */
    public function show(Feedback $feedback)
    {
        $supervisor = auth()->user();
        
        // Verify the feedback belongs to this supervisor
        if ($feedback->supervisor_id !== $supervisor->id) {
            abort(403, 'This feedback does not belong to you.');
        }
        
        return view('supervisor.feedback.show', compact('feedback'));
    }

    /**
     * Update feedback status
     */
    public function updateStatus(Request $request, Feedback $feedback)
    {
        $supervisor = auth()->user();
        
        // Verify the feedback belongs to this supervisor
        if ($feedback->supervisor_id !== $supervisor->id) {
            abort(403, 'This feedback does not belong to you.');
        }

        $request->validate([
            'status' => 'required|in:pending,addressed,resolved',
        ]);

        $feedback->update([
            'status' => $request->status,
            'addressed_date' => $request->status === 'addressed' ? now() : null,
        ]);

        return back()->with('success', 'Feedback status updated');
    }
}
