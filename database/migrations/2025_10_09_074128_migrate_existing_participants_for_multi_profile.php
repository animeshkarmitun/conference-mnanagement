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
        // Update existing participants with default values for new fields
        \DB::table('participants')->update([
            'profile_name' => null, // Will be set based on user name and conference
            'profile_type' => 'personal',
            'status' => 'active',
            'profile_description' => null,
            'is_primary' => true, // All existing participants are primary
        ]);

        // Set profile names for existing participants
        $participants = \DB::table('participants')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->join('conferences', 'participants.conference_id', '=', 'conferences.id')
            ->select('participants.id', 'users.first_name', 'users.last_name', 'conferences.name as conference_name')
            ->get();

        foreach ($participants as $participant) {
            $profileName = $participant->first_name . ' ' . $participant->last_name . ' - ' . $participant->conference_name;
            
            \DB::table('participants')
                ->where('id', $participant->id)
                ->update(['profile_name' => $profileName]);
        }

        // Ensure only one primary participant per user
        $userIds = \DB::table('participants')->distinct()->pluck('user_id');
        
        foreach ($userIds as $userId) {
            $participants = \DB::table('participants')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($participants->count() > 1) {
                // Keep first participant as primary, set others as non-primary
                $firstParticipant = $participants->first();
                \DB::table('participants')
                    ->where('user_id', $userId)
                    ->where('id', '!=', $firstParticipant->id)
                    ->update(['is_primary' => false]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all participants to default values
        \DB::table('participants')->update([
            'profile_name' => null,
            'profile_type' => 'personal',
            'status' => 'active',
            'profile_description' => null,
            'is_primary' => true,
        ]);
    }
};
