<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResearchStage;
use App\Models\Meeting;
use App\Models\Proposal;
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
     * Show the admin dashboard
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalStudents = User::students()->count();
        $totalSupervisors = User::supervisors()->count();
        $totalAdmins = User::admins()->count();


        // Get recent users
        $recentUsers = User::latest()->take(5)->get();

        // Get students by program
        $studentsByProgram = User::students()
            ->selectRaw('program, COUNT(*) as count')
            ->groupBy('program')
            ->get();

        // Get students by year
        $studentsByYear = User::students()
            ->selectRaw('year, COUNT(*) as count')
            ->groupBy('year')
            ->get();

        // Get document submission statistics (Proposal, Data Collection, Report)
        $studentsByStage = Proposal::selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->get();


        // Get unassigned students
        $unassignedStudents = User::students()->whereNull('supervisor_id')->get();

        // Get supervisor performance data
        $supervisors = User::supervisors()->with(['assignedStudents', 'performanceApprovedBy'])->get();
        $supervisorPerformance = [];

        foreach ($supervisors as $supervisor) {
            $assignedStudents = $supervisor->assignedStudents;
            $assignedStudentIds = $assignedStudents->pluck('id');

            // Get concept notes by type
            $totalConceptNotes = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'concept_notes')
                ->count();
            $approvedConceptNotes = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'concept_notes')
                ->where('status', 'approved')
                ->count();

            // Get proposals by type
            $totalProposals = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'proposal')
                ->count();
            $approvedProposals = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'proposal')
                ->where('status', 'approved')
                ->count();

            $totalDataCollection = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'data_collection')
                ->count();
            $approvedDataCollection = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'data_collection')
                ->where('status', 'approved')
                ->count();

            $totalReports = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'report')
                ->count();
            $approvedReports = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'report')
                ->where('status', 'approved')
                ->count();

            // Get completed students (with approved report)
            $completedStudents = Proposal::whereIn('student_id', $assignedStudentIds)
                ->where('document_type', 'report')
                ->where('status', 'approved')
                ->distinct('student_id')
                ->count();

            // Calculate completion rate
            $completionRate = $assignedStudents->count() > 0
                ? round(($completedStudents / $assignedStudents->count()) * 100, 1)
                : 0;

            $supervisorPerformance[] = [
                'supervisor' => $supervisor,
                'assigned_students_count' => $assignedStudents->count(),
                'total_concept_notes' => $totalConceptNotes,
                'approved_concept_notes' => $approvedConceptNotes,
                'total_proposals' => $totalProposals,
                'approved_proposals' => $approvedProposals,
                'total_data_collection' => $totalDataCollection,
                'approved_data_collection' => $approvedDataCollection,
                'total_reports' => $totalReports,
                'approved_reports' => $approvedReports,
                'completed_students' => $completedStudents,
                'completion_rate' => $completionRate,
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalSupervisors',
            'totalAdmins',
            'recentUsers',
            'studentsByProgram',
            'studentsByYear',
            'studentsByStage',
            'unassignedStudents',
            'supervisorPerformance'
        ));
    }

    /**
     * Approve supervisor performance
     */
    public function approvePerformance(Request $request)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:ssris_users,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        if (! auth()->user()->isAdmin() && ! auth()->user()->isHod()) {
            return $this->performanceResponse($request, false, 'You are not authorized to approve supervisor performance.', 403);
        }

        $supervisor = User::where('role', 'supervisor')->findOrFail($request->supervisor_id);

        if ($supervisor->role !== 'supervisor') {
            return $this->performanceResponse($request, false, 'Only supervisors can be approved.', 422);
        }

        if (! $supervisor->canApprovePerformance()) {
            return $this->performanceResponse(
                $request,
                false,
                'Approval is only allowed when completion rate is 100%. Current rate: '.$supervisor->getCompletionRate().'%.',
                422
            );
        }

        $previousStatus = $supervisor->performance_approval_status ?? 'pending';

        $supervisor->update([
            'performance_approval_status' => 'approved',
            'performance_signed_at' => now(),
            'performance_hod_remarks' => $request->input('remarks'),
            'performance_approved_by' => auth()->user()->id,
        ]);

        $message = $previousStatus === 'rejected'
            ? 'Supervisor performance status updated from rejected to approved successfully.'
            : ($previousStatus === 'approved'
                ? 'Supervisor performance approval was refreshed successfully.'
                : 'Supervisor performance approved successfully.');

        return $this->performanceResponse($request, true, $message);
    }

    public function rejectPerformance(Request $request)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:ssris_users,id',
            'remarks' => 'required|string|max:500',
        ]);

        if (! auth()->user()->isAdmin() && ! auth()->user()->isHod()) {
            return $this->performanceResponse($request, false, 'You are not authorized to reject supervisor performance.', 403);
        }

        $supervisor = User::findOrFail($request->supervisor_id);

        if ($supervisor->role !== 'supervisor') {
            return $this->performanceResponse($request, false, 'Only supervisors can be rejected.', 422);
        }

        $previousStatus = $supervisor->performance_approval_status ?? 'pending';

        $supervisor->update([
            'performance_approval_status' => 'rejected',
            'performance_signed_at' => now(),
            'performance_hod_remarks' => $request->input('remarks'),
            'performance_approved_by' => auth()->user()->id,
        ]);

        $message = $previousStatus === 'rejected'
            ? 'Supervisor rejection reason updated successfully.'
            : ($previousStatus === 'approved'
                ? 'Supervisor performance status updated from approved to rejected successfully.'
                : 'Supervisor performance rejected successfully.');

        return $this->performanceResponse($request, true, $message);
    }

    private function performanceResponse(Request $request, bool $success, string $message, int $errorStatus = 400)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $success ? 200 : $errorStatus);
        }

        return redirect()
            ->route('admin.reports.index')
            ->with($success ? 'success' : 'error', $message);
    }
}
