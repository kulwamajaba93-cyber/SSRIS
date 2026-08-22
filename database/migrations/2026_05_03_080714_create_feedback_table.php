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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('research_projects');
            $table->foreignId('student_id')->constrained('ssris_users');
            $table->foreignId('supervisor_id')->constrained('ssris_users');
            $table->foreignId('proposal_id')->nullable()->constrained('proposals');
            $table->foreignId('meeting_id')->nullable()->constrained('meetings');
            $table->enum('type', ['proposal_feedback', 'meeting_feedback', 'general_feedback'])->default('general_feedback');
            $table->string('title');
            $table->text('comments');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'addressed', 'resolved'])->default('pending');
            $table->text('action_required')->nullable();
            $table->date('due_date')->nullable();
            $table->date('addressed_date')->nullable();
            $table->text('student_response')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['supervisor_id', 'type']);
            $table->index(['project_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
