<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $attendeeTypeId = DB::table('participant_types')->where('name', 'attendee')->value('id');
        $speakerTypeId = DB::table('participant_types')->where('name', 'speaker')->value('id');

        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);
        Participant::create([
            'user_id' => $user->id,
            'conference_id' => 1,
            'participant_type_id' => $attendeeTypeId,
            'dietary_needs' => 'vegetarian',
            'travel_intent' => true,
        ]);
        $user = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);
        Participant::create([
            'user_id' => $user->id,
            'conference_id' => 1,
            'participant_type_id' => $speakerTypeId,
            'dietary_needs' => 'none',
            'travel_intent' => false,
        ]);
    }
} 