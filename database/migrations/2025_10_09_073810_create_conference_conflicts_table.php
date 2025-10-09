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
        Schema::create('conference_conflicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('conference_id')->constrained()->onDelete('cascade');
            $table->foreignId('conflicting_conference_id')->constrained('conferences')->onDelete('cascade');
            $table->enum('conflict_type', ['date_overlap', 'schedule_conflict', 'venue_conflict', 'travel_conflict'])->default('date_overlap');
            $table->text('conflict_details')->nullable();
            $table->enum('status', ['pending', 'resolved', 'ignored'])->default('pending');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['participant_id', 'status']);
            $table->index(['conference_id', 'conflict_type']);
            $table->unique(['user_id', 'participant_id', 'conference_id', 'conflicting_conference_id'], 'unique_conflict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_conflicts');
    }
};
