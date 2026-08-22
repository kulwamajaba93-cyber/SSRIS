<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PHONE NUMBER CHECK ===\n\n";

// Check total users
$totalUsers = App\Models\SsrisUser::count();
echo "Total users: {$totalUsers}\n";

// Check users with phone
$usersWithPhone = App\Models\SsrisUser::whereNotNull('phone')->get();
echo "Users with phone: {$usersWithPhone->count()}\n\n";

// Show users with phone
if ($usersWithPhone->count() > 0) {
    echo "Users with phone numbers:\n";
    foreach ($usersWithPhone as $user) {
        echo "- {$user->name} ({$user->role}): {$user->phone}\n";
    }
} else {
    echo "No users have phone numbers\n";
}

// Show students without phone
$studentsWithoutPhone = App\Models\SsrisUser::where('role', 'student')->whereNull('phone')->get();
echo "\nStudents without phone: {$studentsWithoutPhone->count()}\n";
if ($studentsWithoutPhone->count() > 0) {
    echo "Students missing phone:\n";
    foreach ($studentsWithoutPhone as $student) {
        echo "- {$student->name} ({$student->username})\n";
    }
}

// Show supervisors without phone
$supervisorsWithoutPhone = App\Models\SsrisUser::where('role', 'supervisor')->whereNull('phone')->get();
echo "\nSupervisors without phone: {$supervisorsWithoutPhone->count()}\n";
if ($supervisorsWithoutPhone->count() > 0) {
    echo "Supervisors missing phone:\n";
    foreach ($supervisorsWithoutPhone as $supervisor) {
        echo "- {$supervisor->name} ({$supervisor->email})\n";
    }
}
