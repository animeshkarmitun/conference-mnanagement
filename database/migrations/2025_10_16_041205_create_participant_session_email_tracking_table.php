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
        Schema::create('participant_session_email_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->integer('email_send_count')->default(0);
            $table->timestamp('last_email_sent_at')->nullable();
            $table->json('email_recipients')->nullable(); // Store email addresses for this participant
            $table->timestamps();
            
            // Ensure unique combination of session and participant
            $table->unique(['session_id', 'participant_id'], 'pset_session_participant_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_session_email_tracking');
    }
};
