<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\User;
use App\Models\Role;
use App\Models\Conference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalParticipantsSeeder extends Seeder
{
    public function run(): void
    {
        $attendeeTypeId = DB::table('participant_types')->where('name', 'attendee')->value('id');
        $speakerTypeId = DB::table('participant_types')->where('name', 'speaker')->value('id');
        $organizerTypeId = DB::table('participant_types')->where('name', 'organizer')->value('id');

        // Get upcoming conferences
        $conferences = Conference::where('start_date', '>', now())->get();
        
        if ($conferences->isEmpty()) {
            $this->command->info('No upcoming conferences found. Please create conferences first.');
            return;
        }

        $participants = [
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'vegan',
                'travel_intent' => true,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@example.com',
                'participant_type' => 'speaker',
                'dietary_needs' => 'none',
                'travel_intent' => false,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'vegetarian',
                'travel_intent' => true,
                'registration_status' => 'pending'
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@example.com',
                'participant_type' => 'speaker',
                'dietary_needs' => 'gluten_free',
                'travel_intent' => true,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Miller',
                'email' => 'robert.miller@example.com',
                'participant_type' => 'organizer',
                'dietary_needs' => 'none',
                'travel_intent' => false,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Garcia',
                'email' => 'lisa.garcia@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'halal',
                'travel_intent' => true,
                'registration_status' => 'pending'
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Martinez',
                'email' => 'james.martinez@example.com',
                'participant_type' => 'speaker',
                'dietary_needs' => 'none',
                'travel_intent' => true,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Jennifer',
                'last_name' => 'Anderson',
                'email' => 'jennifer.anderson@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'vegetarian',
                'travel_intent' => false,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Christopher',
                'last_name' => 'Taylor',
                'email' => 'christopher.taylor@example.com',
                'participant_type' => 'organizer',
                'dietary_needs' => 'vegan',
                'travel_intent' => true,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Amanda',
                'last_name' => 'Thomas',
                'email' => 'amanda.thomas@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'none',
                'travel_intent' => true,
                'registration_status' => 'pending'
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Hernandez',
                'email' => 'daniel.hernandez@example.com',
                'participant_type' => 'speaker',
                'dietary_needs' => 'gluten_free',
                'travel_intent' => false,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Michelle',
                'last_name' => 'Moore',
                'email' => 'michelle.moore@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'vegetarian',
                'travel_intent' => true,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Jackson',
                'email' => 'kevin.jackson@example.com',
                'participant_type' => 'organizer',
                'dietary_needs' => 'none',
                'travel_intent' => false,
                'registration_status' => 'approved'
            ],
            [
                'first_name' => 'Stephanie',
                'last_name' => 'Martin',
                'email' => 'stephanie.martin@example.com',
                'participant_type' => 'attendee',
                'dietary_needs' => 'vegan',
                'travel_intent' => true,
                'registration_status' => 'pending'
            ],
            [
                'first_name' => 'Andrew',
                'last_name' => 'Lee',
                'email' => 'andrew.lee@example.com',
                'participant_type' => 'speaker',
                'dietary_needs' => 'halal',
                'travel_intent' => true,
                'registration_status' => 'approved'
            ]
        ];

        foreach ($participants as $participantData) {
            // Create or get user
            $user = User::firstOrCreate(
                ['email' => $participantData['email']],
                [
                    'first_name' => $participantData['first_name'],
                    'last_name' => $participantData['last_name'],
                    'password' => bcrypt('password'),
                ]
            );

            // Assign role based on participant type
            $roleName = $participantData['participant_type'];
            $role = Role::where('name', $roleName)->first();
            if ($role && !$user->roles()->where('name', $roleName)->exists()) {
                $user->roles()->attach($role->id);
            }

            // Get participant type ID
            $participantTypeId = DB::table('participant_types')->where('name', $participantData['participant_type'])->value('id');

            // Assign to a random upcoming conference
            $conference = $conferences->random();

            // Create participant
            Participant::firstOrCreate(
                ['user_id' => $user->id, 'conference_id' => $conference->id],
                [
                    'participant_type_id' => $participantTypeId,
                    'dietary_needs' => $participantData['dietary_needs'],
                    'travel_intent' => $participantData['travel_intent'],
                    'registration_status' => $participantData['registration_status'],
                ]
            );
        }

        $this->command->info('Additional participants seeded successfully!');
    }
}
