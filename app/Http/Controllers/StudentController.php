<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user is a student
        if (!$user->isStudent()) {
            return redirect()->route('login')->with('error', 'Access denied. Student access required.');
        }

        $supervisor = $user->supervisor;
        
        return view('student.dashboard', compact('user', 'supervisor'));
    }
}
