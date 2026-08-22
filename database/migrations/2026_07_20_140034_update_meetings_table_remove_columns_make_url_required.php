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
        try {
            Schema::table('meetings', function (Blueprint $table) {
                // Try to drop foreign key if it exists
                $table->dropForeign(['interaction_id']);
            });
        } catch (\Exception $e) {
            // Ignore if foreign key doesn't exist
        }

        Schema::table('meetings', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('meetings', 'interaction_id')) {
                $table->dropColumn('interaction_id');
            }
            if (Schema::hasColumn('meetings', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->foreignId('interaction_id')->nullable()->constrained()->onDelete('set null');
            $table->string('meeting_url')->nullable()->change();
        });
    }
};
