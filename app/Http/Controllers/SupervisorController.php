<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SsrisUser;

class SupervisorController extends Controller
{
    /**
     * Display supervisor dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user is a supervisor
        if (!$user->isSupervisor()) {
            return redirect()->route('login')->with('error', 'Access denied. Supervisor access required.');
        }

        $students = $user->students;
        $stats = [
            'totalStudents' => $students->count(),
            'bbictStudents' => $students->where('program', SsrisUser::PROGRAM_BBICT)->count(),
            'bhrmStudents' => $students->where('program', SsrisUser::PROGRAM_BHRM)->count(),
            'batStudents' => $students->where('program', SsrisUser::PROGRAM_BAT)->count(),
        ];
        
        return view('supervisor.dashboard', compact('user', 'students', 'stats'));
    }
}
