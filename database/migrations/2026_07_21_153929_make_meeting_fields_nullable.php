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
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('meeting_url')->nullable()->change();
            $table->text('discussion_notes')->nullable()->change();
            $table->text('action_points')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('meeting_url')->nullable(false)->change();
            $table->text('discussion_notes')->nullable(false)->change();
            $table->text('action_points')->nullable(false)->change();
        });
    }
};
