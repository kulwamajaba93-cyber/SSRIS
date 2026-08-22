<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Proposal;
use App\Models\ResearchStage;

echo "=== Checking Proposals Table ===\n";
$proposals = Proposal::all();
echo "Total Proposals: " . $proposals->count() . "\n";

$byType = Proposal::selectRaw('document_type, COUNT(*) as count')
    ->groupBy('document_type')
    ->get();
foreach($byType as $item) {
    echo "- Type: {$item->document_type}, Count: {$item->count}\n";
}

echo "\n=== Checking ResearchStage Table ===\n";
$stages = ResearchStage::all();
echo "Total Stages: " . $stages->count() . "\n";
foreach($stages as $item) {
    echo "- User ID: {$item->user_id}, Stage: {$item->stage}\n";
}
