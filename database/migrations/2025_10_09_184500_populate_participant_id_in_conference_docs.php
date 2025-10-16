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
        // Only run if conference_docs table exists and has data
        if (Schema::hasTable('conference_docs') && DB::table('conference_docs')->count() > 0) {
            // Update existing conference docs to link to the primary participant
            // for each conference
            DB::statement("
                UPDATE conference_docs cd 
                JOIN participants p ON cd.conference_id = p.conference_id 
                    AND p.is_primary = 1
                    AND p.status = 'active'
                SET cd.participant_id = p.id
                WHERE cd.participant_id IS NULL
            ");
            
            // For conference docs where no primary participant exists,
            // link to the first active participant for that conference
            DB::statement("
                UPDATE conference_docs cd 
                JOIN (
                    SELECT cd2.id as doc_id, 
                           (SELECT p2.id 
                            FROM participants p2 
                            WHERE p2.conference_id = cd2.conference_id 
                              AND p2.status = 'active'
                            ORDER BY p2.created_at ASC 
                            LIMIT 1) as participant_id
                    FROM conference_docs cd2
                    WHERE cd2.participant_id IS NULL
                ) as first_participants ON cd.id = first_participants.doc_id
                SET cd.participant_id = first_participants.participant_id
                WHERE first_participants.participant_id IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all participant_id to null
        DB::table('conference_docs')->update(['participant_id' => null]);
    }
};