<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of the student's meetings
     */
    public function index()
    {
        $student = auth()->user();
        
        // Get meetings from both old student_id and new pivot
        $meetingsFromPivot = $student->meetingParticipations()->with('supervisor')->latest()->get();
        $meetingsFromOld = $student->meetings()->with('supervisor')->latest()->get();
        
        // Merge and unique by id
        $meetings = $meetingsFromPivot->merge($meetingsFromOld)->unique('id')->sortByDesc('created_at');
        
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.meetings.index', compact('meetings'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Display the specified meeting
     */
    public function show(Meeting $meeting)
    {
        $student = auth()->user();
        
        // Verify the meeting belongs to this student (either old way or pivot)
        $isInOld = $meeting->student_id === $student->id;
        $isInPivot = $meeting->students()->where('user_id', $student->id)->exists();
        
        if (!$isInOld && !$isInPivot) {
            abort(403, 'This meeting does not belong to you.');
        }
        
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        // Load supervisor relation
        $meeting->load('supervisor');
        
        return view('student.meetings.show', compact('meeting'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }
}
