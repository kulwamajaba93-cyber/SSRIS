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
        // Drop the old research_stages table if it exists
        Schema::dropIfExists('research_stages');
        
        // Create the new research_stages master table
        Schema::create('research_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('step_number')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_stages');
        
        // Optionally recreate the old table (for rollback)
        Schema::create('research_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('ssris_users')->onDelete('cascade');
            $table->enum('stage', [
                'proposal_submitted',
                'under_review',
                'revision',
                'approved',
                'in_progress',
                'completed'
            ])->default('proposal_submitted');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
