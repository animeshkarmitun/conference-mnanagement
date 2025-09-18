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
        Schema::create('email_threads', function (Blueprint $table) {
            $table->string('id', 36)->primary(); // UUID primary key
            $table->string('subject', 500);
            $table->string('participant_email', 255);
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_activity_at')->useCurrent();
            
            // Add basic indexes
            $table->index('conference_id');
            $table->index('last_activity_at');
            
            // Add foreign key constraint
            $table->foreign('conference_id')->references('id')->on('conferences')->onDelete('set null');
        });
        
        // Add participant_email index separately after table creation
        DB::statement('ALTER TABLE email_threads ADD INDEX participant_email_index (participant_email(191))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_threads');
    }
};
