<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SsrisUser;
use App\Models\ResearchProject;
use App\Models\Proposal;
use App\Models\Meeting;
use App\Models\Feedback;

class ResearchDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing demo students
        $student = SsrisUser::where('username', 'MOCU/BBICT/1089/23')->first();
        $supervisor = SsrisUser::where('email', 'supervisor@mocu.ac.tz')->first();

        if (!$student || !$supervisor) {
            $this->command->error('Demo student or supervisor not found. Please run DemoDataSeeder first.');
            return;
        }

        // Create research project for the student
        $project = ResearchProject::create([
            'title' => 'Impact of Digital Technology on Business Performance in Tanzania',
            'description' => 'This research investigates the impact of digital technology adoption on business performance among small and medium enterprises in Tanzania.',
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'status' => ResearchProject::STATUS_UNDER_REVIEW,
            'start_date' => now()->subMonths(2),
            'expected_completion_date' => now()->addMonths(4),
            'research_area' => 'Digital Transformation',
            'keywords' => 'digital technology, business performance, SMEs, Tanzania',
        ]);

        // Create proposals (at least 2 as required)
        $proposal1 = Proposal::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'title' => 'Research Proposal - Digital Technology Impact',
            'abstract' => 'This study examines how digital technology adoption affects business performance in Tanzanian SMEs.',
            'file_path' => 'proposals/sample_proposal_v1.pdf',
            'original_filename' => 'research_proposal_v1.pdf',
            'version' => 1,
            'status' => Proposal::STATUS_REVIEWED,
            'submission_notes' => 'Initial proposal submission',
            'submitted_at' => now()->subMonths(1),
            'reviewed_at' => now()->subWeeks(3),
            'reviewed_by' => $supervisor->id,
            'review_comments' => 'Good start. Need to expand literature review section.',
        ]);

        $proposal2 = Proposal::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'title' => 'Research Proposal - Digital Technology Impact (Revised)',
            'abstract' => 'This study examines how digital technology adoption affects business performance in Tanzanian SMEs, with expanded literature review and methodology.',
            'file_path' => 'proposals/sample_proposal_v2.pdf',
            'original_filename' => 'research_proposal_v2.pdf',
            'version' => 2,
            'status' => Proposal::STATUS_UNDER_REVIEW,
            'submission_notes' => 'Revised proposal addressing supervisor feedback',
            'submitted_at' => now()->subWeeks(2),
        ]);

        // Create meetings (at least 1 as required)
        $meeting1 = Meeting::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'title' => 'Initial Project Discussion',
            'description' => 'Discussion about research objectives and methodology',
            'meeting_date' => now()->subMonths(1),
            'type' => Meeting::TYPE_PROPOSAL_DISCUSSION,
            'status' => Meeting::STATUS_COMPLETED,
            'discussion_notes' => 'Discussed research objectives, scope, and initial methodology.',
            'action_points' => '1. Expand literature review\n2. Refine research questions\n3. Prepare detailed methodology',
            'supervisor_notes' => 'Student shows good understanding of research topic. Focus on practical implementation.',
            'scheduled_at' => now()->subMonths(1),
            'completed_at' => now()->subMonths(1),
            'location' => 'Supervisor Office',
            'agenda' => '1. Research objectives\n2. Methodology discussion\n3. Timeline planning',
        ]);

        $meeting2 = Meeting::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'title' => 'Progress Review Meeting',
            'description' => 'Review of proposal progress and feedback discussion',
            'meeting_date' => now()->subWeeks(3),
            'type' => Meeting::TYPE_PROGRESS_REVIEW,
            'status' => Meeting::STATUS_COMPLETED,
            'discussion_notes' => 'Reviewed first draft of proposal. Provided feedback on structure and content.',
            'action_points' => '1. Expand literature review section\n2. Add more recent references\n3. Clarify research methodology',
            'supervisor_notes' => 'Good progress. Student has incorporated most of the initial feedback.',
            'scheduled_at' => now()->subWeeks(3),
            'completed_at' => now()->subWeeks(3),
            'location' => 'Virtual Meeting',
            'agenda' => '1. Proposal review\n2. Feedback discussion\n3. Next steps',
        ]);

        $meeting3 = Meeting::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'title' => 'Feedback Session',
            'description' => 'Discussion of supervisor feedback on revised proposal',
            'meeting_date' => now()->addWeeks(1),
            'type' => Meeting::TYPE_FEEDBACK_SESSION,
            'status' => Meeting::STATUS_SCHEDULED,
            'discussion_notes' => 'Scheduled to discuss feedback on revised proposal.',
            'action_points' => 'TBD',
            'scheduled_at' => now()->addWeeks(1),
            'location' => 'Supervisor Office',
            'agenda' => '1. Review revised proposal\n2. Address outstanding issues\n3. Plan next steps',
        ]);

        // Create feedback (at least 1 as required)
        $feedback1 = Feedback::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'proposal_id' => $proposal1->id,
            'type' => Feedback::TYPE_PROPOSAL_FEEDBACK,
            'title' => 'Literature Review Expansion Required',
            'comments' => 'The literature review section needs to be expanded with more recent studies (2020-2024) and include more international perspectives on digital transformation in SMEs.',
            'priority' => Feedback::PRIORITY_HIGH,
            'status' => Feedback::STATUS_RESOLVED,
            'action_required' => 'Add at least 10 more recent references and expand the literature review by 2-3 pages.',
            'due_date' => now()->subWeeks(2),
            'addressed_date' => now()->subWeeks(1),
            'student_response' => 'I have expanded the literature review section with 15 additional recent references and added international perspectives from studies in Kenya, Nigeria, and South Africa.',
        ]);

        $feedback2 = Feedback::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'meeting_id' => $meeting2->id,
            'type' => Feedback::TYPE_MEETING_FEEDBACK,
            'title' => 'Methodology Clarification Needed',
            'comments' => 'The research methodology section needs more detail on data collection methods and sampling techniques.',
            'priority' => Feedback::PRIORITY_MEDIUM,
            'status' => Feedback::STATUS_PENDING,
            'action_required' => 'Provide detailed explanation of survey design, sample size calculation, and data analysis methods.',
            'due_date' => now()->addWeeks(1),
        ]);

        $feedback3 = Feedback::create([
            'project_id' => $project->id,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'type' => Feedback::TYPE_GENERAL_FEEDBACK,
            'title' => 'Timeline Adjustment',
            'comments' => 'Consider adjusting the research timeline to allow more time for data collection and analysis.',
            'priority' => Feedback::PRIORITY_LOW,
            'status' => Feedback::STATUS_PENDING,
            'action_required' => 'Review and update project timeline accordingly.',
            'due_date' => now()->addWeeks(2),
        ]);

        $this->command->info('Research data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Research Project');
        $this->command->info('- 2 Proposals (v1, v2)');
        $this->command->info('- 3 Meetings (2 completed, 1 scheduled)');
        $this->command->info('- 3 Feedback items (1 resolved, 2 pending)');
        $this->command->info('');
        $this->command->info('Student Dashboard Statistics:');
        $this->command->info('- Total Meetings Attended: 2');
        $this->command->info('- Total Proposal Revisions: 1');
        $this->command->info('- Total Feedback Received: 3');
    }
}
