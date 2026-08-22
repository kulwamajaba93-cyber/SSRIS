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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, delivered, failed
            $table->string('type')->default('general'); // document_submission, approval, rejection, feedback, meeting, etc.
            $table->foreignId('user_id')->nullable()->constrained('ssris_users')->onDelete('cascade');
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->onDelete('cascade');
            $table->foreignId('meeting_id')->nullable()->constrained('meetings')->onDelete('cascade');
            $table->text('api_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
