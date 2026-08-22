<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\Meeting;
use App\Models\Feedback;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
     * Show the supervisor dashboard
     */
    public function index()
    {
        $supervisor = auth()->user();
        
        // Get assigned students with their latest proposal
        $assignedStudents = $supervisor->assignedStudents()
            ->with([
                'proposals' => function ($query) {
                    $query->latest();
                }
            ])
            ->get();
        
        // Count students by program
        $studentsByProgram = $supervisor->assignedStudents()
            ->selectRaw('program, COUNT(*) as count')
            ->groupBy('program')
            ->get();
            
        // Count students by year
        $studentsByYear = $supervisor->assignedStudents()
            ->selectRaw('year, COUNT(*) as count')
            ->groupBy('year')
            ->get();

        // Get all proposals from assigned students
        $allDocuments = Proposal::whereIn('student_id', $assignedStudents->pluck('id'))
            ->with('student')
            ->latest()
            ->get();

        // Filter documents by type
        $conceptNotesDocuments = $allDocuments->where('document_type', 'concept_notes');
        $proposalDocuments = $allDocuments->where('document_type', 'proposal');
        $dataCollectionDocuments = $allDocuments->where('document_type', 'data_collection');
        $reportDocuments = $allDocuments->where('document_type', 'report');

        // Get all meetings (using both old student_id and new pivot)
        $meetings = Meeting::where('supervisor_id', $supervisor->id)
            ->with(['student', 'students'])
            ->latest()
            ->get();

        // Get all feedback given
        $feedback = Feedback::where('supervisor_id', $supervisor->id)
            ->with('student', 'proposal')
            ->latest()
            ->get();

        // Calculate statistics
        $totalStudents = $assignedStudents->count();
        $totalProposals = $allDocuments->count();
        $totalMeetings = $meetings->count();
        $totalFeedback = $feedback->count();
        
        // Calculate individual document type counts
        $conceptNotesCount = $conceptNotesDocuments->count();
        $proposalCount = $proposalDocuments->count();
        $dataCollectionCount = $dataCollectionDocuments->count();
        $reportCount = $reportDocuments->count();

        return view('supervisor.dashboard', compact(
            'supervisor',
            'assignedStudents',
            'studentsByProgram',
            'studentsByYear',
            'allDocuments',
            'conceptNotesDocuments',
            'proposalDocuments',
            'dataCollectionDocuments',
            'reportDocuments',
            'meetings',
            'feedback',
            'totalStudents',
            'totalProposals',
            'totalMeetings',
            'totalFeedback',
            'conceptNotesCount',
            'proposalCount',
            'dataCollectionCount',
            'reportCount'
        ));
    }

    /**
     * Show assigned students page
     */
    public function students()
    {
        $supervisor = auth()->user();
        $assignedStudents = $supervisor->assignedStudents()
            ->with(['proposals' => function ($query) {
                $query->latest();
            }])
            ->get();

        return view('supervisor.students.index', compact('assignedStudents'));
    }

    /**
     * Show proposals page
     */
    public function proposals(Request $request)
    {
        $supervisor = auth()->user();
        $assignedStudents = $supervisor->assignedStudents()->get();
        $documentType = $request->query('type');

        // Default to proposal if no type is specified
        if (!$documentType) {
            $documentType = 'proposal';
        }

        $query = Proposal::whereIn('student_id', $assignedStudents->pluck('id'))
            ->with('student')
            ->latest();

        // Filter by document type if specified
        if ($documentType && in_array($documentType, ['concept_notes', 'proposal', 'data_collection', 'report'])) {
            $query->where('document_type', $documentType);
        }

        $proposals = $query->get();

        return view('supervisor.proposals.index', compact('proposals', 'documentType'));
    }

    /**
     * Show meetings page
     */
    public function meetings()
    {
        $supervisor = auth()->user();
        $assignedStudents = $supervisor->assignedStudents()->get();
        $meetings = Meeting::where('supervisor_id', $supervisor->id)
            ->with(['student', 'students'])
            ->latest()
            ->get();

        return view('supervisor.meetings.index', compact('meetings', 'assignedStudents'));
    }

    /**
     * Download proposal document
     */
    public function downloadProposal(Proposal $proposal)
    {
        $supervisor = auth()->user();

        // Check if supervisor is assigned to the student
        $assignedStudentIds = $supervisor->assignedStudents()->pluck('id')->toArray();
        if (!in_array($proposal->student_id, $assignedStudentIds)) {
            abort(403, 'You are not authorized to download this proposal.');
        }

        // Check if file exists
        if (!$proposal->file_path) {
            abort(404, 'No file attached to this proposal.');
        }

        // Try to find the file in public storage
        if (!Storage::disk('public')->exists($proposal->file_path)) {
            abort(404, 'File not found in storage: ' . $proposal->file_path);
        }

        return Storage::disk('public')->download($proposal->file_path, $proposal->original_filename);
    }
}
