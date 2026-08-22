<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interaction;

class InteractionController extends Controller
{
    public function index()
    {
        $student = auth()->user();
        $interactions = Interaction::where('student_id', $student->id)
            ->latest()
            ->get();

        $pendingFeedbackCount = $student->feedbackReceived()->where('status', 'pending')->count();

        return view('student.interactions.index', compact('interactions'))
            ->with('pendingFeedbackCount', $pendingFeedbackCount);
    }
}
