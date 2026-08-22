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
            // Drop existing foreign key for project_id first
            $table->dropForeign(['project_id']);
            
            // Make project_id nullable
            $table->foreignId('project_id')->nullable()->change();
            
            // Re-add the foreign key with nullable
            $table->foreign('project_id')->nullable()->references('id')->on('research_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            
            $table->foreignId('project_id')->nullable(false)->change();
            
            $table->foreign('project_id')->references('id')->on('research_projects')->onDelete('cascade');
        });
    }
};
