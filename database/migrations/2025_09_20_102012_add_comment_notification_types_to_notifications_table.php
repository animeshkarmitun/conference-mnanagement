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
        // Update the enum to include comment notification types
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('MissingDocuments', 'SessionUpdate', 'TravelUpdate', 'General', 'TaskUpdate', 'ConferenceUpdate', 'ProfileUpdate', 'comment_added', 'participant_comment') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to previous enum values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('MissingDocuments', 'SessionUpdate', 'TravelUpdate', 'General', 'TaskUpdate', 'ConferenceUpdate', 'ProfileUpdate') NOT NULL");
    }
};