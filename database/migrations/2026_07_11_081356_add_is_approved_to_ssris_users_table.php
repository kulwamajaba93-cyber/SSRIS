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
        Schema::table('ssris_users', function (Blueprint $table) {
            // Add is_approved column if not exists
            if (!Schema::hasColumn('ssris_users', 'is_approved')) {
                $table->boolean('is_approved')->default(false)->after('role');
            }
            
            // Add department column if not exists
            if (!Schema::hasColumn('ssris_users', 'department')) {
                $table->string('department')->nullable()->after('program');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ssris_users', function (Blueprint $table) {
            if (Schema::hasColumn('ssris_users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
            if (Schema::hasColumn('ssris_users', 'department')) {
                $table->dropColumn('department');
            }
        });
    }
};
