<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MeetingController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Show the form for creating a new meeting
     */
    public function create()
    {
        $supervisor = auth()->user();
        $assignedStudents = $supervisor->assignedStudents;
        return view('supervisor.meetings.create', compact('assignedStudents'));
    }

    /**
     * Store a newly created meeting
     */
    public function store(Request $request, SmsService $smsService)
    {
        $actionType = $request->input('action_type', 'create');
        // Determine which student input is being used
        $useSingleStudent = $request->has('student_id');
        $useMultipleStudents = $request->has('student_ids');

        // Validate
        $validationRules = [
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
        ];

        if ($useMultipleStudents) {
            $validationRules['student_ids'] = 'required|array|min:1';
            $validationRules['student_ids.*'] = 'exists:ssris_users,id';
            $validationRules['meeting_url'] = 'required|string';
        } elseif ($useSingleStudent) {
            $validationRules['student_id'] = 'required|exists:ssris_users,id';
            $validationRules['discussion_notes'] = 'nullable|string';
            $validationRules['action_points'] = 'nullable|string';
        } else {
            return back()->with('error', 'No students selected');
        }

        $request->validate($validationRules);

        $supervisor = auth()->user();
        $assignedStudentIds = $supervisor->assignedStudents->pluck('id')->toArray();

        // Get student IDs to process
        $studentIds = [];
        if ($useMultipleStudents) {
            $studentIds = $request->student_ids;
        } else {
            $studentIds = [$request->student_id];
        }

        // Verify all selected students are assigned
        foreach ($studentIds as $studentId) {
            if (!in_array($studentId, $assignedStudentIds)) {
                return back()->with('error', 'One or more students are not assigned to you.');
            }
        }

        // Prepare meeting data
        $meetingData = [
            'supervisor_id' => $supervisor->id,
            'title' => $request->title,
            'meeting_date' => $request->meeting_date,
            'status' => $actionType === 'record' ? Meeting::STATUS_COMPLETED : Meeting::STATUS_SCHEDULED,
        ];

        if ($useMultipleStudents && $request->has('meeting_url')) {
            $meetingUrl = $request->meeting_url;
            if (!str_starts_with($meetingUrl, 'http://') && !str_starts_with($meetingUrl, 'https://')) {
                $meetingUrl = 'https://' . $meetingUrl;
            }
            $meetingData['meeting_url'] = $meetingUrl;
        }

        if ($useSingleStudent && $request->has('discussion_notes')) {
            $meetingData['discussion_notes'] = $request->discussion_notes;
        }
        if ($useSingleStudent && $request->has('action_points')) {
            $meetingData['action_points'] = $request->action_points;
        }

        // Create meeting
        $meeting = Meeting::create($meetingData);

        // Attach students to meeting
        $meeting->students()->attach($studentIds);

        // If single student, also set student_id column for backward compatibility
        if ($useSingleStudent) {
            $meeting->update(['student_id' => $request->student_id]);
        }

        // Only send SMS for CREATE action, not RECORD
        if ($actionType === 'create') {
            // Parse date and send SMS
            $meetingDate = Carbon::parse($meeting->meeting_date);
            $formattedDate = $meetingDate->format('d/m/Y');
            $formattedTime = $meetingDate->format('H:i');
            foreach ($studentIds as $studentId) {
                $student = User::find($studentId);
                if ($student) {
                    $smsService->sendMeetingNotification($student, $meeting, $formattedDate, $formattedTime);
                }
            }
        }

        return redirect()->route('supervisor.meetings.index')
            ->with('success', $actionType === 'record' ? 'Meeting recorded successfully' : 'Meeting created successfully');
    }

    /**
     * Display the specified meeting
     */
    public function show(Meeting $meeting)
    {
        $supervisor = auth()->user();
        
        // Verify the meeting belongs to this supervisor
        if ($meeting->supervisor_id !== $supervisor->id) {
            abort(403, 'This meeting does not belong to you.');
        }
        
        return view('supervisor.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified meeting
     */
    public function edit(Meeting $meeting)
    {
        $supervisor = auth()->user();
        
        // Verify the meeting belongs to this supervisor
        if ($meeting->supervisor_id !== $supervisor->id) {
            abort(403, 'This meeting does not belong to you.');
        }
        
        $assignedStudents = $supervisor->assignedStudents;
        return view('supervisor.meetings.edit', compact('meeting', 'assignedStudents'));
    }

    /**
     * Update the specified meeting
     */
    public function update(Request $request, Meeting $meeting)
    {
        $supervisor = auth()->user();
        
        // Verify the meeting belongs to this supervisor
        if ($meeting->supervisor_id !== $supervisor->id) {
            abort(403, 'This meeting does not belong to you.');
        }

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:ssris_users,id',
            'title' => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_url' => 'required|string',
            'discussion_notes' => 'nullable|string',
            'action_points' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $assignedStudentIds = $supervisor->assignedStudents->pluck('id')->toArray();
        // Verify all selected students are assigned to this supervisor
        foreach ($request->student_ids as $studentId) {
            if (!in_array($studentId, $assignedStudentIds)) {
                return back()->with('error', 'One or more students are not assigned to you.');
            }
        }

        // Ensure meeting URL has https:// if not present
        $meetingUrl = $request->meeting_url;
        if (!str_starts_with($meetingUrl, 'http://') && !str_starts_with($meetingUrl, 'https://')) {
            $meetingUrl = 'https://' . $meetingUrl;
        }
        
        $meeting->update([
            'title' => $request->title,
            'meeting_date' => $request->meeting_date,
            'meeting_url' => $meetingUrl,
            'discussion_notes' => $request->discussion_notes,
            'action_points' => $request->action_points,
            'status' => $request->status,
        ]);

        // Sync students
        $meeting->students()->sync($request->student_ids);

        return redirect()->route('supervisor.meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully');
    }
}
