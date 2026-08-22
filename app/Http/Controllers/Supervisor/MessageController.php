<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of conversations
     */
    public function index()
    {
        $supervisor = auth()->user();
        $assignedStudents = $supervisor->assignedStudents;

        // Get all students with unread message count (messages sent TO supervisor)
        $studentsWithUnread = [];
        foreach ($assignedStudents as $student) {
            $unreadCount = $supervisor->unreadMessages()
                ->where('sender_id', $student->id)
                ->count();
            $studentsWithUnread[] = [
                'student' => $student,
                'unread_count' => $unreadCount,
            ];
        }

        return view('supervisor.messages.index', compact('studentsWithUnread'));
    }

    /**
     * Display conversation with a specific student
     */
    public function show(User $student)
    {
        $supervisor = auth()->user();

        // Verify the student is assigned to this supervisor
        if ($student->supervisor_id !== $supervisor->id) {
            return back()->with('error', 'This student is not assigned to you.');
        }

        // Get conversation with student
        $messages = Message::between($supervisor->id, $student->id)
            ->latest()
            ->get();

        // Mark received messages as read
        $supervisor->unreadMessages()->where('sender_id', $student->id)->update([
            'read' => true,
            'read_at' => now()
        ]);

        return view('supervisor.messages.show', compact('messages', 'student'));
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request, User $student)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $supervisor = auth()->user();

        // Verify the student is assigned to this supervisor
        if ($student->supervisor_id !== $supervisor->id) {
            return back()->with('error', 'This student is not assigned to you.');
        }

        Message::create([
            'sender_id' => $supervisor->id,
            'receiver_id' => $student->id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent successfully');
    }
}
