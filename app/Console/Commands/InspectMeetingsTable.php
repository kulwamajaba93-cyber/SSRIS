<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InspectMeetingsTable extends Command
{
    protected $signature = 'db:inspect-meetings';
    protected $description = 'Check and modify the meetings table';

    public function handle()
    {
        $this->info('Checking meetings table structure');
        
        $columns = DB::select('SHOW COLUMNS FROM meetings');
        $this->info('Current columns:');
        foreach ($columns as $col) {
            $this->line("- {$col->Field} ({$col->Type}) NULL: " . ($col->Null === 'YES' ? 'Yes' : 'No'));
        }
        
        $hasMeetingUrl = collect($columns)->contains('Field', 'meeting_url');
        $hasInteractionId = collect($columns)->contains('Field', 'interaction_id');
        $hasType = collect($columns)->contains('Field', 'type');
        
        $this->info("\nHas meeting_url: " . ($hasMeetingUrl ? 'Yes' : 'No'));
        $this->info('Has interaction_id: ' . ($hasInteractionId ? 'Yes' : 'No'));
        $this->info('Has type: ' . ($hasType ? 'Yes' : 'No'));

        // Now let's add the missing columns
        if (!$hasMeetingUrl) {
            $this->info('Adding interaction_id and meeting_url...');
            try {
                DB::statement('ALTER TABLE meetings ADD COLUMN interaction_id BIGINT UNSIGNED NULL');
                DB::statement('ALTER TABLE meetings ADD COLUMN meeting_url VARCHAR(255) NULL');
                DB::statement('ALTER TABLE meetings ADD CONSTRAINT meetings_interaction_id_foreign FOREIGN KEY (interaction_id) REFERENCES interactions(id) ON DELETE SET NULL');
                $this->info('Columns added successfully');
            } catch (\Exception $e) {
                $this->error('Error adding columns: ' . $e->getMessage());
            }
        }

        // Create meeting_student table
        if (!Schema::hasTable('meeting_student')) {
            $this->info('Creating meeting_student table...');
            try {
                Schema::create('meeting_student', function ($table) {
                    $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                });
                $this->info('meeting_student table created');
            } catch (\Exception $e) {
                $this->error('Error creating table: ' . $e->getMessage());
            }
        }

        // Now drop type and interaction_id, make meeting_url not null
        if ($hasType) {
            $this->info('Dropping type, interaction_id, and making meeting_url NOT NULL...');
            try {
                // Drop foreign key first if it exists
                $foreignKeys = DB::select('
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = "meetings" 
                    AND COLUMN_NAME = "interaction_id"
                ');
                
                if (count($foreignKeys) > 0) {
                    DB::statement("ALTER TABLE meetings DROP FOREIGN KEY {$foreignKeys[0]->CONSTRAINT_NAME}");
                }
                
                DB::statement('ALTER TABLE meetings DROP COLUMN interaction_id, DROP COLUMN type');
                DB::statement('ALTER TABLE meetings MODIFY COLUMN meeting_url VARCHAR(255) NOT NULL');
                
                $this->info('Columns dropped and meeting_url updated');
            } catch (\Exception $e) {
                $this->error('Error modifying table: ' . $e->getMessage());
                $this->line($e->getTraceAsString());
            }
        }
    }
}
