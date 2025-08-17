<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $attendeeTypeId = DB::table('participant_types')->where('name', 'attendee')->value('id');
        $speakerTypeId = DB::table('participant_types')->where('name', 'speaker')->value('id');

        $user = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => bcrypt('password'),
            ]
        );
        
        // Assign attendee role
        $attendeeRole = Role::where('name', 'attendee')->first();
        if ($attendeeRole && !$user->roles()->where('name', 'attendee')->exists()) {
            $user->roles()->attach($attendeeRole->id);
        }
        
        Participant::firstOrCreate(
            ['user_id' => $user->id, 'conference_id' => 1],
            [
                'participant_type_id' => $attendeeTypeId,
                'dietary_needs' => 'vegetarian',
                'travel_intent' => true,
            ]
        );
        
        $user = User::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'password' => bcrypt('password'),
            ]
        );
        
        // Assign speaker role
        $speakerRole = Role::where('name', 'speaker')->first();
        if ($speakerRole && !$user->roles()->where('name', 'speaker')->exists()) {
            $user->roles()->attach($speakerRole->id);
        }
        
        Participant::firstOrCreate(
            ['user_id' => $user->id, 'conference_id' => 1],
            [
                'participant_type_id' => $speakerTypeId,
                'dietary_needs' => 'none',
                'travel_intent' => false,
            ]
        );
    }
} 