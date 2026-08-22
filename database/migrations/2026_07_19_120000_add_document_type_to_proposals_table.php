<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the existing enum column to include 'concept_notes'
        DB::statement("ALTER TABLE proposals MODIFY COLUMN document_type ENUM('proposal', 'data_collection', 'report', 'concept_notes') NOT NULL DEFAULT 'proposal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE proposals MODIFY COLUMN document_type ENUM('proposal', 'data_collection', 'report') NOT NULL DEFAULT 'proposal'");
    }
};
