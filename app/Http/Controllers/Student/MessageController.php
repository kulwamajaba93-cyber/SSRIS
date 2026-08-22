<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of messages
     */
    public function index()
    {
        $student = auth()->user();
        $supervisor = $student->supervisor;

        if (!$supervisor) {
            return back()->with('error', 'You need to be assigned a supervisor to send messages.');
        }

        // Get conversation with supervisor
        $messages = Message::between($student->id, $supervisor->id)
            ->latest()
            ->get();

        // Mark received messages as read
        $student->unreadMessages()->where('sender_id', $supervisor->id)->update([
            'read' => true,
            'read_at' => now()
        ]);

        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();

        return view('student.messages.index', compact('messages', 'supervisor'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $student = auth()->user();
        $supervisor = $student->supervisor;

        if (!$supervisor) {
            return back()->with('error', 'You need to be assigned a supervisor to send messages.');
        }

        Message::create([
            'sender_id' => $student->id,
            'receiver_id' => $supervisor->id,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent successfully');
    }
}
