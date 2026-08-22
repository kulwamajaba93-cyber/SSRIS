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
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('student_id')->constrained('ssris_users');
            $table->foreignId('supervisor_id')->constrained('ssris_users');
            $table->enum('status', [
                'proposal_submitted',
                'under_review', 
                'revision',
                'approved',
                'in_progress',
                'completed'
            ])->default('proposal_submitted');
            $table->date('start_date')->nullable();
            $table->date('expected_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->text('research_area')->nullable();
            $table->text('keywords')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['supervisor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_projects');
    }
};
