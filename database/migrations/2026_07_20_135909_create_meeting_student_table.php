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
        if (!Schema::hasTable('meeting_student')) {
            Schema::create('meeting_student', function (Blueprint $table) {
                $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained('ssris_users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_student');
    }
};
