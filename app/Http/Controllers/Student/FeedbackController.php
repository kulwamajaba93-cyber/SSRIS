<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display a listing of the student's feedback
     */
    public function index()
    {
        $student = auth()->user();
        $feedback = $student->feedbackReceived()->latest()->get();
        $pendingFeedback = $feedback->where('status', 'pending')->count();
        
        // Mark pending feedback as addressed when viewed
        $feedback->where('status', 'pending')->each(function ($item) {
            $item->update([
                'status' => 'addressed',
                'addressed_date' => now(),
            ]);
        });
        
        // Refresh feedback collection
        $feedback = $student->feedbackReceived()->latest()->get();
        
        return view('student.feedback.index', compact('feedback', 'pendingFeedback'))
            ->with('pendingFeedbackCount', $pendingFeedback);
    }

    /**
     * Display the specified feedback
     */
    public function show(Feedback $feedback)
    {
        $student = auth()->user();
        
        // Verify the feedback belongs to this student
        if ($feedback->student_id !== $student->id) {
            abort(403, 'This feedback does not belong to you.');
        }
        
        // Mark as addressed if still pending
        if ($feedback->status === 'pending') {
            $feedback->update([
                'status' => 'addressed',
                'addressed_date' => now(),
            ]);
        }
        
        // Get pending feedback count for sidebar
        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();
        
        return view('student.feedback.show', compact('feedback'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }
}
