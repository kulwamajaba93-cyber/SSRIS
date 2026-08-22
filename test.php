<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Users ===" . PHP_EOL;
print_r(\App\Models\User::get(['id', 'name', 'email', 'phone', 'role', 'supervisor_id'])->toArray());
echo PHP_EOL;
echo "=== SMS Logs ===" . PHP_EOL;
print_r(\App\Models\SmsLog::latest()->get()->toArray());
echo PHP_EOL;
echo "=== Proposals ===" . PHP_EOL;
print_r(\App\Models\Proposal::latest()->get()->toArray());
