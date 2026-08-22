<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Meetings table structure ===\n";
$columns = DB::select("PRAGMA table_info(meetings)");
foreach ($columns as $col) {
    echo "- {$col->name}: {$col->type}\n";
}

echo "\n=== Meeting records ===\n";
$meetings = DB::table('meetings')->get();
foreach ($meetings as $m) {
    echo "ID: {$m->id}\n";
    foreach ((array)$m as $key => $value) {
        echo "  {$key}: " . var_export($value, true) . "\n";
    }
    echo "\n";
}
