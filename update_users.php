<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Updating users...\n";
// Update supervisor
$supervisor = \App\Models\User::find(2);
$supervisor->phone = '255712345678'; // Sample phone number
$supervisor->save();

// Update student
$student = \App\Models\User::find(3);
$student->phone = '255787654321'; // Sample phone number
$student->save();

echo "Users updated!\n";
echo "Supervisor: " . $supervisor->phone . "\n";
echo "Student: " . $student->phone . "\n";
