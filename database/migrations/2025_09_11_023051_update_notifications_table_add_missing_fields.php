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
        Schema::table('notifications', function (Blueprint $table) {
            // Add missing columns
            $table->string('related_model')->nullable()->after('type');
            $table->unsignedBigInteger('related_id')->nullable()->after('related_model');
            $table->string('action_url')->nullable()->after('related_id');
        });

        // Update the enum to include all notification types
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('MissingDocuments', 'SessionUpdate', 'TravelUpdate', 'General', 'TaskUpdate', 'ConferenceUpdate', 'ProfileUpdate') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['related_model', 'related_id', 'action_url']);
        });

        // Revert the enum to original values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('MissingDocuments', 'SessionUpdate', 'TravelUpdate', 'General') NOT NULL");
    }
};