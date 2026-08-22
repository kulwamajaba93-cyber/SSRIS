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
            if (!Schema::hasColumn('ssris_users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
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
            if (Schema::hasColumn('ssris_users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('ssris_users', 'department')) {
                $table->dropColumn('department');
            }
        });
    }
};
