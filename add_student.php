<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SSRIS Student Adder ===\n\n";

if ($argc < 3) {
    echo "Usage: php add_student.php \"Full Name\" \"Registration Number\" \"Phone Number\"\n";
    echo "Example: php add_student.php \"John Doe\" \"MOCU/BBICT/1500/23\" \"+255712345678\"\n";
    exit(1);
}

$name = $argv[1];
$registrationNumber = $argv[2];
$phone = $argv[3] ?? null;

try {
    $user = App\Models\User::createStudent($name, $registrationNumber, $phone);
    echo "✅ SUCCESS: Student '{$user->name}' created successfully!\n";
    echo "   Registration: {$user->username}\n";
    echo "   Password: mocu.{$user->program}." . explode('/', $registrationNumber)[2] . "." . explode('/', $registrationNumber)[3] . "\n";
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
