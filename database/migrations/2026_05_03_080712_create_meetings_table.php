<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('research_projects');
            $table->foreignId('student_id')->constrained('ssris_users');
            $table->foreignId('supervisor_id')->constrained('ssris_users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('meeting_date');
            $table->enum('type', ['proposal_discussion', 'progress_review', 'feedback_session', 'final_review'])->default('progress_review');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('discussion_notes')->nullable();
            $table->text('action_points')->nullable();
            $table->text('supervisor_notes')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('location')->nullable();
            $table->text('agenda')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'meeting_date']);
            $table->index(['supervisor_id', 'meeting_date']);
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
