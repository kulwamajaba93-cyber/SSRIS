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
        if (!Schema::hasTable('interactions')) {
            Schema::create('interactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('ssris_users')->onDelete('cascade');
                $table->foreignId('supervisor_id')->constrained('ssris_users')->onDelete('cascade');
                $table->foreignId('proposal_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('feedback_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('student_progress_id')->nullable()->constrained()->onDelete('set null');
                $table->string('action_type'); // submission, feedback, approval, revision_request
                $table->text('notes')->nullable();
                $table->string('status')->default('in_review'); // in_review, revision_required, completed
                $table->string('document_reference')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
