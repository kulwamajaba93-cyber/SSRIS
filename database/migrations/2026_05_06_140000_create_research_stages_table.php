<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('research_stages');
    }
};
