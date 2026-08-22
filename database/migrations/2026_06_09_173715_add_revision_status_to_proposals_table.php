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
        Schema::table('proposals', function (Blueprint $table) {
            $table->enum('status', ['pending', 'under_review', 'reviewed', 'approved', 'rejected', 'revision'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->enum('status', ['pending', 'under_review', 'reviewed', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};
