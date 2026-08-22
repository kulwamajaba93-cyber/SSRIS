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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('research_projects');
            $table->foreignId('student_id')->constrained('ssris_users');
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->integer('version')->default(1);
            $table->enum('status', ['pending', 'under_review', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->text('submission_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('ssris_users');
            $table->text('review_comments')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'version']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
