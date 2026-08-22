<?php

echo "=== Users ===" . PHP_EOL;
print_r(\App\Models\User::get(['id', 'name', 'email', 'phone', 'role', 'supervisor_id'])->toArray());

echo PHP_EOL . PHP_EOL . "=== SMS Logs ===" . PHP_EOL;
print_r(\App\Models\SmsLog::latest()->get()->toArray());

echo PHP_EOL . PHP_EOL . "=== Proposals ===" . PHP_EOL;
print_r(\App\Models\Proposal::latest()->get()->toArray());
