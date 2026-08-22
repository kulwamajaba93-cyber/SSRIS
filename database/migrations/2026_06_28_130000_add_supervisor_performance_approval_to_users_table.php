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
            // Add supervisor performance approval columns
            $table->enum('performance_approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('performance_signed_at')->nullable();
            $table->text('performance_hod_remarks')->nullable();
            $table->foreignId('performance_approved_by')->nullable()->constrained('ssris_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ssris_users', function (Blueprint $table) {
            $table->dropForeign(['performance_approved_by']);
            $table->dropColumn([
                'performance_approval_status',
                'performance_signed_at',
                'performance_hod_remarks',
                'performance_approved_by'
            ]);
        });
    }
};
