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
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('ssris_users')->onDelete('cascade');
            $table->foreignId('stage_id')->constrained('research_stages')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'locked'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('ssris_users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            // To ensure one entry per student per stage
            $table->unique(['student_id', 'stage_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
    }
};
