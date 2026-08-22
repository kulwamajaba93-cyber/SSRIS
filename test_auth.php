<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Test password creation and verification
echo "Testing SSRIS Authentication...\n\n";

// Test 1: Check if demo users exist
$admin = User::where('email', 'admin@mocu.ac.tz')->first();
if ($admin) {
    echo "✅ Admin user found: " . $admin->name . "\n";
    echo "   Email: " . $admin->email . "\n";
    echo "   Role: " . $admin->role . "\n";
    
    // Test password verification
    if (Hash::check('admin123', $admin->password)) {
        echo "✅ Admin password verification: PASSED\n";
    } else {
        echo "❌ Admin password verification: FAILED\n";
    }
} else {
    echo "❌ Admin user not found\n";
}

echo "\n";

// Test 2: Check supervisor
$supervisor = User::where('email', 'supervisor@mocu.ac.tz')->first();
if ($supervisor) {
    echo "✅ Supervisor user found: " . $supervisor->name . "\n";
    echo "   Email: " . $supervisor->email . "\n";
    echo "   Role: " . $supervisor->role . "\n";
    
    if (Hash::check('supervisor123', $supervisor->password)) {
        echo "✅ Supervisor password verification: PASSED\n";
    } else {
        echo "❌ Supervisor password verification: FAILED\n";
    }
} else {
    echo "❌ Supervisor user not found\n";
}

echo "\n";

// Test 3: Check student
$student = User::where('username', 'MOCU/BBICT/1089/23')->first();
if ($student) {
    echo "✅ Student user found: " . $student->name . "\n";
    echo "   Username: " . $student->username . "\n";
    echo "   Role: " . $student->role . "\n";
    echo "   Program: " . $student->program . "\n";
    
    if (Hash::check('mocu.bbict.1089.23', $student->password)) {
        echo "✅ Student password verification: PASSED\n";
    } else {
        echo "❌ Student password verification: FAILED\n";
        echo "   Expected password: mocu.bbict.1089.23\n";
        echo "   Stored hash: " . $student->password . "\n";
    }
} else {
    echo "❌ Student user not found\n";
}

echo "\nAuthentication test completed.\n";
