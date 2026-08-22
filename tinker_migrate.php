<?php
echo "Checking meetings table structure: \n";
$columns = DB::select('SHOW COLUMNS FROM meetings');
foreach ($columns as $col) {
    echo "- {$col->Field} ({$col->Type})\n";
}

echo "\nChecking if meeting_url exists: " . (Schema::hasColumn('meetings', 'meeting_url') ? 'Yes' : 'No') . "\n";

if (!Schema::hasColumn('meetings', 'meeting_url')) {
    echo "Adding meeting_url and interaction_id...\n";
    DB::statement('ALTER TABLE meetings ADD COLUMN interaction_id BIGINT UNSIGNED NULL');
    DB::statement('ALTER TABLE meetings ADD COLUMN meeting_url VARCHAR(255) NULL');
    DB::statement('ALTER TABLE meetings ADD CONSTRAINT meetings_interaction_id_foreign FOREIGN KEY (interaction_id) REFERENCES interactions(id) ON DELETE SET NULL');
    echo "Done adding columns.\n";
}

echo "\nChecking if meeting_student exists: " . (Schema::hasTable('meeting_student') ? 'Yes' : 'No') . "\n";
if (!Schema::hasTable('meeting_student')) {
    echo "Creating meeting_student...\n";
    Schema::create('meeting_student', function (Blueprint $table) {
        $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
    });
    echo "Created meeting_student.\n";
}

echo "\nChecking if type exists: " . (Schema::hasColumn('meetings', 'type') ? 'Yes' : 'No') . "\n";
if (Schema::hasColumn('meetings', 'type')) {
    echo "Dropping type and interaction_id, setting meeting_url NOT NULL...\n";
    try {
        DB::statement('ALTER TABLE meetings DROP FOREIGN KEY meetings_interaction_id_foreign');
    } catch (\Exception $e) {
        echo "No foreign key to drop: {$e->getMessage()}\n";
    }
    DB::statement('ALTER TABLE meetings DROP COLUMN interaction_id, DROP COLUMN type');
    DB::statement('ALTER TABLE meetings MODIFY COLUMN meeting_url VARCHAR(255) NOT NULL');
    echo "Done modifying.\n";
}
echo "\nAll checks complete!\n";
