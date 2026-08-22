<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Test route for authentication debugging
Route::get('/test-auth', function () {
    $results = [];
    
    // Test Admin
    $admin = User::where('email', 'admin@mocu.ac.tz')->first();
    if ($admin) {
        $results['admin'] = [
            'found' => true,
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => $admin->role,
            'password_check' => Hash::check('admin123', $admin->password)
        ];
    } else {
        $results['admin'] = ['found' => false];
    }
    
    // Test Supervisor
    $supervisor = User::where('email', 'supervisor@mocu.ac.tz')->first();
    if ($supervisor) {
        $results['supervisor'] = [
            'found' => true,
            'name' => $supervisor->name,
            'email' => $supervisor->email,
            'role' => $supervisor->role,
            'password_check' => Hash::check('supervisor123', $supervisor->password)
        ];
    } else {
        $results['supervisor'] = ['found' => false];
    }
    
    // Test Student
    $student = User::where('username', 'MOCU/BBICT/1089/23')->first();
    if ($student) {
        $results['student'] = [
            'found' => true,
            'name' => $student->name,
            'username' => $student->username,
            'role' => $student->role,
            'program' => $student->program,
            'password_check' => Hash::check('mocu.bbict.1089.23', $student->password)
        ];
    } else {
        $results['student'] = ['found' => false];
    }
    
    return response()->json($results);
});
