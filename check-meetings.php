<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Meeting;

echo "=== Meetings in Database ===\n";
$meetings = Meeting::all();

foreach ($meetings as $m) {
    echo "- ID: {$m->id}\n";
    echo "  Title: {$m->title}\n";
    echo "  Meeting URL: " . var_export($m->meeting_url, true) . "\n";
    echo "  Status: {$m->status}\n";
    echo "  Supervisor ID: {$m->supervisor_id}\n";
    echo "  Student ID: {$m->student_id}\n";
    echo "\n";
}

if ($meetings->count() === 0) {
    echo "No meetings found.\n";
}
