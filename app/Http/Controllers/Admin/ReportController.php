<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\Feedback;
use App\Models\ResearchStage;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display comprehensive reports and analytics
     */
    public function index()
    {
        // Document submissions distribution
        $studentsByStage = Proposal::selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->get();

        // Supervisor performance metrics
        $supervisors = User::supervisors()->with('assignedStudents')->get();
        $supervisorPerformance = [];
        
        foreach ($supervisors as $supervisor) {
            $assignedStudents = $supervisor->assignedStudents;
            $studentIds = $assignedStudents->pluck('id');
            
            $totalProposals = Proposal::whereIn('student_id', $studentIds)->count();
            $approvedProposals = Proposal::whereIn('student_id', $studentIds)
                ->where('status', 'approved')
                ->count();

            // Data collection metrics
            $totalDataCollection = Proposal::whereIn('student_id', $studentIds)
                ->where('document_type', 'data_collection')
                ->count();
            $approvedDataCollection = Proposal::whereIn('student_id', $studentIds)
                ->where('document_type', 'data_collection')
                ->where('status', 'approved')
                ->count();

            // Report metrics
            $totalReports = Proposal::whereIn('student_id', $studentIds)
                ->where('document_type', 'report')
                ->count();
            $approvedReports = Proposal::whereIn('student_id', $studentIds)
                ->where('document_type', 'report')
                ->where('status', 'approved')
                ->count();

            $totalFeedback = Feedback::where('supervisor_id', $supervisor->id)->count();
            
            // Calculate completion rate (students with approved report)
            $completedStudents = Proposal::whereIn('student_id', $studentIds)
                ->where('document_type', 'report')
                ->where('status', 'approved')
                ->distinct('student_id')
                ->count();
            
            $supervisorPerformance[] = [
                'supervisor' => $supervisor,
                'assigned_students_count' => $assignedStudents->count(),
                'total_proposals' => $totalProposals,
                'approved_proposals' => $approvedProposals,
                'total_data_collection' => $totalDataCollection,
                'approved_data_collection' => $approvedDataCollection,
                'total_reports' => $totalReports,
                'approved_reports' => $approvedReports,
                'total_feedback' => $totalFeedback,
                'completed_students' => $completedStudents,
                'completion_rate' => $assignedStudents->count() > 0
                    ? round(($completedStudents / $assignedStudents->count()) * 100, 2)
                    : 0,
            ];
        }

        // Overall system statistics
        $totalStudents = User::students()->count();
        $totalSupervisors = User::supervisors()->count();
        $totalProposals = Proposal::count();
        $totalFeedback = Feedback::count();

        return view('admin.reports.index', compact(
            'studentsByStage',
            'supervisorPerformance',
            'totalStudents',
            'totalSupervisors',
            'totalProposals',
            'totalFeedback'
        ));
    }
}
