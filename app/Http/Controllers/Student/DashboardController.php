<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Show the student dashboard
     */
    public function index()
    {
        $student = auth()->user();
        
        // Get assigned supervisor
        $supervisor = $student->supervisor;
        
        // Get proposals
        $proposals = $student->proposals()->latest()->get();
        
        // Get feedback and mark pending ones as addressed when viewed
        $feedback = $student->feedbackReceived()->latest()->get();
        
        // Mark pending feedback as addressed when student views dashboard
        $pendingFeedback = $feedback->where('status', 'pending');
        foreach ($pendingFeedback as $item) {
            $item->update([
                'status' => 'addressed',
                'addressed_date' => now(),
            ]);
        }
        
        // Refresh feedback collection after updates
        $feedback = $student->feedbackReceived()->latest()->get();
        
        // Get meetings
        $meetings = $student->meetings()->latest()->get();
        
        // Calculate statistics
        $totalMeetings = $meetings->count();
        $totalRevisions = $proposals->where('status', 'revision')->count();
        $totalFeedback = $feedback->count();
        $pendingFeedback = $feedback->where('status', 'pending')->count();
        
        // Count documents by type for sidebar
        $conceptNotesCount = $proposals->where('document_type', 'concept_notes')->count();
        $proposalCount = $proposals->where('document_type', 'proposal')->count();
        $dataCollectionCount = $proposals->where('document_type', 'data_collection')->count();
        $reportCount = $proposals->where('document_type', 'report')->count();

        return view('student.dashboard', compact(
            'student',
            'supervisor',
            'proposals',
            'feedback',
            'meetings',
            'totalMeetings',
            'totalRevisions',
            'totalFeedback',
            'pendingFeedback',
            'conceptNotesCount',
            'proposalCount',
            'dataCollectionCount',
            'reportCount'
        ))->with('pendingFeedbackCount', $pendingFeedback);
    }
}
