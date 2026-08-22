<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interaction;
use App\Models\User;

class InteractionController extends Controller
{
    public function index()
    {
        $interactions = Interaction::latest()->get();
        $students = User::where('role', 'student')->get();
        $supervisors = User::where('role', 'supervisor')->get();

        return view('admin.interactions.index', compact('interactions', 'students', 'supervisors'));
    }

    public function show(User $student)
    {
        $interactions = Interaction::where('student_id', $student->id)
            ->latest()
            ->get();

        return view('admin.interactions.show', compact('interactions', 'student'));
    }
}
