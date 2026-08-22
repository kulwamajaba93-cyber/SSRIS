<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SsrisUser;
use App\Models\ResearchProject;
use App\Models\Proposal;
use App\Models\Meeting;
use App\Models\Feedback;

class StudentDashboardController extends Controller
{
    /**
     * Display the comprehensive student dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ensure user is a student
        if (!$user->isStudent()) {
            return redirect()->route('login')->with('error', 'Access denied. Student access required.');
        }

        // Get or create student's research project
        $project = $user->currentProject();
        if (!$project) {
            // Create a default project for the student
            $project = ResearchProject::create([
                'title' => 'Research Project - ' . $user->name,
                'description' => 'Research project description',
                'student_id' => $user->id,
                'supervisor_id' => $user->supervisor_id,
                'status' => ResearchProject::STATUS_PROPOSAL_SUBMITTED,
                'start_date' => now(),
                'research_area' => 'General Research',
                'keywords' => 'research, study',
            ]);
        }

        // Get statistics for dashboard (IMPORTANT FROM PROPOSAL)
        $stats = [
            'totalMeetings' => $project->completed_meetings_count,
            'totalRevisions' => $project->total_proposals_count - 1, // v1 doesn't count as revision
            'totalFeedback' => $project->pending_feedback_count,
        ];

        // Get proposals with latest first
        $proposals = $project->proposals()->orderBy('version', 'desc')->get();

        // Get meetings with latest first
        $meetings = $project->meetings()->orderBy('meeting_date', 'desc')->get();

        // Get feedback with latest first
        $feedback = $project->feedback()->with(['supervisor', 'proposal', 'meeting'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get supervisor info
        $supervisor = $user->supervisor;

        // Prepare data for dashboard components
        $dashboardData = [
            'user' => $user,
            'project' => $project,
            'supervisor' => $supervisor,
            'stats' => $stats,
            'proposals' => $proposals,
            'meetings' => $meetings,
            'feedback' => $feedback,
            'recentMeetings' => $meetings->take(5), // Latest 5 meetings
            'recentFeedback' => $feedback->take(5), // Latest 5 feedback
            'latestProposal' => $proposals->first(),
        ];

        return view('student.comprehensive-dashboard', $dashboardData);
    }

    /**
     * Upload proposal functionality.
     */
    public function uploadProposal(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'proposal_file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            'submission_notes' => 'nullable|string',
        ]);

        $project = $user->currentProject();
        if (!$project) {
            return response()->json(['error' => 'No project found'], 404);
        }

        // Get next version number
        $latestVersion = $project->proposals()->max('version') ?? 0;
        $newVersion = $latestVersion + 1;

        // Handle file upload
        $filePath = null;
        $originalFilename = null;
        
        if ($request->hasFile('proposal_file')) {
            $file = $request->file('proposal_file');
            $originalFilename = $file->getClientOriginalName();
            $filename = 'proposal_' . $user->id . '_v' . $newVersion . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('proposals', $filename, 'public');
        }

        // Create proposal
        $proposal = Proposal::create([
            'project_id' => $project->id,
            'student_id' => $user->id,
            'title' => $request->title,
            'abstract' => $request->abstract,
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'version' => $newVersion,
            'status' => Proposal::STATUS_PENDING,
            'submission_notes' => $request->submission_notes,
            'submitted_at' => now(),
        ]);

        // Update project status if needed
        if ($project->status === ResearchProject::STATUS_PROPOSAL_SUBMITTED) {
            $project->status = ResearchProject::STATUS_UNDER_REVIEW;
            $project->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Proposal uploaded successfully',
            'proposal' => $proposal,
        ]);
    }

    /**
     * Get meeting details.
     */
    public function getMeetingDetails(Meeting $meeting)
    {
        $user = Auth::user();
        
        if (!$user->isStudent() || $meeting->student_id !== $user->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return response()->json([
            'meeting' => $meeting->load(['supervisor']),
        ]);
    }

    /**
     * Get feedback details.
     */
    public function getFeedbackDetails(Feedback $feedback)
    {
        $user = Auth::user();
        
        if (!$user->isStudent() || $feedback->student_id !== $user->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return response()->json([
            'feedback' => $feedback->load(['supervisor', 'proposal', 'meeting']),
        ]);
    }

    /**
     * Mark feedback as addressed.
     */
    public function addressFeedback(Request $request, Feedback $feedback)
    {
        $user = Auth::user();
        
        if (!$user->isStudent() || $feedback->student_id !== $user->id) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $request->validate([
            'student_response' => 'required|string',
        ]);

        $feedback->student_response = $request->student_response;
        $feedback->status = Feedback::STATUS_ADDRESSED;
        $feedback->addressed_date = now();
        $feedback->save();

        return response()->json([
            'success' => true,
            'message' => 'Feedback marked as addressed',
        ]);
    }

    /**
     * Get dashboard statistics (AJAX endpoint).
     */
    public function getDashboardStats()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $project = $user->currentProject();
        
        if (!$project) {
            return response()->json([
                'totalMeetings' => 0,
                'totalRevisions' => 0,
                'totalFeedback' => 0,
            ]);
        }

        return response()->json([
            'totalMeetings' => $project->completed_meetings_count,
            'totalRevisions' => $project->total_proposals_count - 1,
            'totalFeedback' => $project->pending_feedback_count,
        ]);
    }
}
