<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interaction;
use App\Models\User;

class InteractionController extends Controller
{
    public function index()
    {
        $supervisor = auth()->user();
        $interactions = Interaction::where('supervisor_id', $supervisor->id)
            ->latest()
            ->get();

        $students = User::where('supervisor_id', $supervisor->id)->get();

        return view('supervisor.interactions.index', compact('interactions', 'students'));
    }

    public function show(User $student)
    {
        $supervisor = auth()->user();
        if ($student->supervisor_id !== $supervisor->id) {
            abort(403);
        }

        $interactions = Interaction::where('student_id', $student->id)
            ->where('supervisor_id', $supervisor->id)
            ->latest()
            ->get();

        return view('supervisor.interactions.show', compact('interactions', 'student'));
    }
}
