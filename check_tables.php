<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE TABLES CHECK ===\n\n";

// Get all tables
$tables = DB::select('SHOW TABLES');
echo "Tables in ssris database:\n";
foreach ($tables as $table) {
    $tableName = $table->Tables_in_ssris;
    echo "- {$tableName}\n";
}

echo "\n=== USER RECORDS CHECK ===\n";

// Check ssris_users table
$ssrisUsersCount = DB::table('ssris_users')->count();
echo "Records in ssris_users: {$ssrisUsersCount}\n";

// Check users table
$usersCount = DB::table('users')->count();
echo "Records in users: {$usersCount}\n";

if ($ssrisUsersCount > 0) {
    echo "\nSample records from ssris_users:\n";
    $users = DB::table('ssris_users')->limit(5)->get(['id', 'name', 'role', 'phone', 'username']);
    foreach ($users as $user) {
        echo "- ID: {$user->id}, Name: {$user->name}, Role: {$user->role}, Phone: " . ($user->phone ?? 'NULL') . ", Username: " . ($user->username ?? 'NULL') . "\n";
    }
}

if ($usersCount > 0) {
    echo "\nSample records from users:\n";
    $users = DB::table('users')->limit(5)->get(['id', 'name', 'role', 'phone', 'username']);
    foreach ($users as $user) {
        echo "- ID: {$user->id}, Name: {$user->name}, Role: {$user->role}, Phone: " . ($user->phone ?? 'NULL') . ", Username: " . ($user->username ?? 'NULL') . "\n";
    }
}
