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
        // Update existing notifications to link to the primary participant
        // for each user-conference combination
        DB::statement("
            UPDATE notifications n 
            JOIN participants p ON n.user_id = p.user_id 
                AND n.conference_id = p.conference_id 
                AND p.is_primary = 1
                AND p.status = 'active'
            SET n.participant_id = p.id
            WHERE n.participant_id IS NULL
        ");
        
        // For notifications where no primary participant exists,
        // link to the first active participant for that user-conference combination
        DB::statement("
            UPDATE notifications n 
            JOIN (
                SELECT n2.id as notification_id, 
                       (SELECT p2.id 
                        FROM participants p2 
                        WHERE p2.user_id = n2.user_id 
                          AND p2.conference_id = n2.conference_id 
                          AND p2.status = 'active'
                        ORDER BY p2.created_at ASC 
                        LIMIT 1) as participant_id
                FROM notifications n2
                WHERE n2.participant_id IS NULL
            ) as first_participants ON n.id = first_participants.notification_id
            SET n.participant_id = first_participants.participant_id
            WHERE first_participants.participant_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all participant_id to null
        DB::table('notifications')->update(['participant_id' => null]);
    }
};