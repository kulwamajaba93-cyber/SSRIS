<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SSRIS User Updater ===\n\n";

if ($argc < 3) {
    echo "Usage: php update_user.php \"User ID\" \"New Name\" [\"New Registration Number\"] [\"New Phone\"]\n";
    echo "Example for student: php update_user.php 1 \"John Doe\" \"MOCU/BBICT/1500/23\" \"+255712345678\"\n";
    echo "Example for supervisor: php update_user.php 2 \"Dr. Smith\" \"\" \"\" \"new.email@mocu.ac.tz\" \"newpassword\"\n";
    exit(1);
}

$userId = $argv[1];
$newName = $argv[2];
$newRegNumber = isset($argv[3]) ? $argv[3] : null;
$newPhone = isset($argv[4]) ? $argv[4] : null;
$newEmail = isset($argv[5]) ? $argv[5] : null;
$newPassword = isset($argv[6]) ? $argv[6] : null;

try {
    $user = App\Models\User::find($userId);
    
    if (!$user) {
        echo "❌ ERROR: User with ID {$userId} not found.\n";
        exit(1);
    }

    echo "Current User:\n";
    echo "  ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Role: {$user->role}\n";
    echo "  Username/Email: " . ($user->username ? $user->username : $user->email) . "\n";
    echo "\n";

    // Update name
    $user->name = $newName;

    // Update based on role
    if ($user->role === 'student') {
        if ($newRegNumber) {
            $user->username = $newRegNumber;
            // Parse registration number to extract components
            $parts = explode('/', $newRegNumber);
            $user->program = isset($parts[1]) ? $parts[1] : '';
            $user->reg_number = (isset($parts[2]) ? $parts[2] : '') . '/' . (isset($parts[3]) ? $parts[3] : '');
            $user->year = isset($parts[3]) ? $parts[3] : '';
        }
        if ($newPhone) {
            $user->phone = $newPhone;
        }
    } else {
        // Supervisor/Admin
        if ($newEmail) {
            $user->email = $newEmail;
        }
        if ($newPhone) {
            $user->phone = $newPhone;
        }
        if ($newPassword) {
            $user->password = Hash::make($newPassword);
        }
    }

    $user->save();

    echo "✅ SUCCESS: User updated successfully!\n";
    echo "   New Name: {$user->name}\n";
    if ($user->role === 'student') {
        echo "   New Registration: {$user->username}\n";
    } else {
        echo "   New Email: {$user->email}\n";
    }
    if ($newPhone) {
        echo "   New Phone: {$user->phone}\n";
    }
    if ($newPassword && $user->role !== 'student') {
        echo "   Password updated\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
