<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentProgress;
use App\Models\Proposal;
use App\Models\ResearchStage;

class ResetResearchProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'progress:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all research progress data to initial state';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('Are you sure you want to reset all research progress data? This will reset all student progress, proposal statuses, and cannot be undone!')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Resetting research progress...');

        // Step 1: Delete all existing StudentProgress records
        $this->info('Deleting existing student progress records...');
        $deletedProgress = StudentProgress::query()->delete();
        $this->info("Deleted {$deletedProgress} student progress records.");

        // Step 2: Reset all Proposal statuses to pending and clear review info
        $this->info('Resetting proposal statuses...');
        $updatedProposals = Proposal::query()->update([
            'status' => 'pending',
            'reviewed_at' => null,
            'reviewed_by' => null,
            'review_comments' => null,
        ]);
        $this->info("Reset {$updatedProposals} proposals to pending status.");

        // Step 3: Re-initialize student progress for all students
        $this->info('Re-initializing student progress...');
        $students = \App\Models\User::students()->get();
        $stages = ResearchStage::orderBy('step_number')->get();

        $initializedCount = 0;
        foreach ($students as $student) {
            foreach ($stages as $stage) {
                StudentProgress::firstOrCreate(
                    ['student_id' => $student->id, 'stage_id' => $stage->id],
                    ['status' => $stage->step_number === 1 ? 'pending' : 'locked']
                );
                $initializedCount++;
            }
        }
        $this->info("Initialized progress for {$students->count()} students across {$stages->count()} stages (total {$initializedCount} records).");

        $this->info('✅ Research progress reset completed successfully!');
    }
}
