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
        Schema::create('ssris_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique(); // For students
            $table->string('email')->nullable()->unique(); // For supervisors/admin
            $table->enum('role', ['admin', 'supervisor', 'student']);
            $table->string('password');
            $table->string('program')->nullable(); // BBICT, BHRM, BAT
            $table->string('reg_number')->nullable(); // 1089
            $table->string('year')->nullable(); // 23
            $table->foreignId('supervisor_id')->nullable()->constrained('ssris_users'); // For student-supervisor assignment
            $table->timestamps();
            
            $table->index(['role', 'program', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ssris_users');
    }
};
