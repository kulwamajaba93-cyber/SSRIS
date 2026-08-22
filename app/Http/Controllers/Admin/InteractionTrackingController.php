<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\Request;

class InteractionTrackingController extends Controller
{
    public function index()
    {
        // Get all students with eager loading (optimized)
        $students = User::students()
            ->with(['supervisor', 'proposals', 'meetingParticipations'])
            ->get();

        // Define document types
        $documentTypes = ['concept_notes', 'proposal', 'data_collection', 'report'];

        return view('admin.interaction-tracking.index', compact('students', 'documentTypes'));
    }

    public function show(User $student)
    {
        // Ensure user is a student
        if (!$student->isStudent()) {
            abort(403);
        }

        $student->load(['supervisor', 'proposals' => function ($query) {
            $query->latest('version');
        }, 'meetingParticipations']);

        $documentTypes = ['concept_notes', 'proposal', 'data_collection', 'report'];

        // Determine current stage
        $currentStage = 'Concept Notes';
        $approvedDocs = $student->proposals->where('status', 'approved')->pluck('document_type')->toArray();
        if (in_array('report', $approvedDocs)) {
            $currentStage = 'Completed';
        } elseif (in_array('data_collection', $approvedDocs)) {
            $currentStage = 'Final Report';
        } elseif (in_array('proposal', $approvedDocs)) {
            $currentStage = 'Data Collection';
        } elseif (in_array('concept_notes', $approvedDocs)) {
            $currentStage = 'Proposal';
        }

        return view('admin.interaction-tracking.show', compact('student', 'documentTypes', 'currentStage'));
    }
}
