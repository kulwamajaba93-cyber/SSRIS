<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SmsService;

echo "Testing SmsService...\n";
$smsService = new SmsService();

echo "Testing send()...\n";
$result = $smsService->send('255712345678', 'This is a test SMS from SSRIS', 'general', 2, null, null);
echo "Result:\n";
print_r($result);

echo "Testing sendDocumentSubmissionNotification()...\n";
$student = \App\Models\User::find(3);
$result2 = $smsService->sendDocumentSubmissionNotification($student, 'Proposal', 3);
echo "Result 2:\n";
print_r($result2);
