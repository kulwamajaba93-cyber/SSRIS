
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
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreignId('project_id')->nullable()->change();
            $table->foreign('project_id')->references('id')->on('research_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreignId('project_id')->change();
            $table->foreign('project_id')->references('id')->on('research_projects')->onDelete('cascade');
        });
    }
};
