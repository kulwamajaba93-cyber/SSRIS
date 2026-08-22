<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Starting migration...\n";

try {
    // Check if meeting_url exists
    echo "Checking for meeting_url column...\n";
    if (!Schema::hasColumn('meetings', 'meeting_url')) {
        echo "Adding interaction_id and meeting_url...\n";
        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('interaction_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meeting_url')->nullable();
        });
        echo "Added interaction_id and meeting_url to meetings table.\n";
    } else {
        echo "Columns interaction_id and meeting_url already exist in meetings table.\n";
    }

    // Create meeting_student table if not exists
    echo "Checking for meeting_student table...\n";
    if (!Schema::hasTable('meeting_student')) {
        echo "Creating meeting_student...\n";
        Schema::create('meeting_student', function (Blueprint $table) {
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
        echo "Created meeting_student pivot table.\n";
    } else {
        echo "meeting_student table already exists.\n";
    }

    // Now update meeting_url to NOT NULL (if possible)
    echo "Checking if type column exists...\n";
    if (Schema::hasColumn('meetings', 'type')) {
        echo "Dropping type and interaction_id, making meeting_url not null...\n";
        Schema::table('meetings', function (Blueprint $table) {
            // First drop foreign key on interaction_id
            $foreignKeys = DB::select("SHOW CREATE TABLE meetings")[0]->{'Create Table'};
            if (str_contains($foreignKeys, 'meetings_interaction_id_foreign')) {
                $table->dropForeign(['interaction_id']);
            }
            $table->dropColumn(['interaction_id', 'type']);
            $table->string('meeting_url')->nullable(false)->change();
        });
        echo "Dropped interaction_id and type, made meeting_url NOT NULL.\n";
    } else {
        echo "Type column already dropped.\n";
    }
    
    echo "All migrations done!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
