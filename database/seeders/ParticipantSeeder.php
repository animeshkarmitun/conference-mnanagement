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
            ['email' => 'john.doe@techcorp.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'password' => bcrypt('password'),
                'organization' => 'TechCorp Solutions',
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
                'travel_intent' => true,
                'bio' => 'Senior software engineer with 8+ years of experience in web development and cloud technologies.',
                'organization' => 'TechCorp Solutions',
                'registration_status' => 'approved',
                'approved' => true,
                'status' => 'active',
                'is_primary' => true,
                'profile_type' => 'professional',
                'profile_name' => 'John Doe - TechCorp'
            ]
        );
        
        $user = User::firstOrCreate(
            ['email' => 'jane.smith@ai-research.org'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'password' => bcrypt('password'),
                'organization' => 'AI Research Institute',
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
                'travel_intent' => false,
                'bio' => 'Leading AI researcher and author of several papers on machine learning applications in healthcare.',
                'organization' => 'AI Research Institute',
                'registration_status' => 'approved',
                'approved' => true,
                'status' => 'active',
                'is_primary' => true,
                'profile_type' => 'speaker',
                'profile_name' => 'Jane Smith - AI Researcher'
            ]
        );

        // Add more participants for different conferences
        $user = User::firstOrCreate(
            ['email' => 'alex.chen@healthcare.org'],
            [
                'first_name' => 'Alex',
                'last_name' => 'Chen',
                'password' => bcrypt('password'),
                'organization' => 'Healthcare Innovations',
            ]
        );
        
        // Assign speaker role
        if (!$user->roles()->where('name', 'speaker')->exists()) {
            $user->roles()->attach($speakerRole->id);
        }
        
        Participant::firstOrCreate(
            ['user_id' => $user->id, 'conference_id' => 2],
            [
                'participant_type_id' => $speakerTypeId,
                'travel_intent' => true,
                'bio' => 'Medical technology expert specializing in telemedicine and remote patient monitoring systems.',
                'organization' => 'Healthcare Innovations',
                'registration_status' => 'approved',
                'approved' => true,
                'status' => 'active',
                'is_primary' => true,
                'profile_type' => 'speaker',
                'profile_name' => 'Alex Chen - Healthcare Expert'
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'sophie.wilson@sustainability.com'],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Wilson',
                'password' => bcrypt('password'),
                'organization' => 'Green Future Initiative',
            ]
        );
        
        // Assign attendee role
        if (!$user->roles()->where('name', 'attendee')->exists()) {
            $user->roles()->attach($attendeeRole->id);
        }
        
        Participant::firstOrCreate(
            ['user_id' => $user->id, 'conference_id' => 4],
            [
                'participant_type_id' => $attendeeTypeId,
                'travel_intent' => true,
                'bio' => 'Environmental consultant and sustainability advocate with expertise in green technology implementation.',
                'organization' => 'Green Future Initiative',
                'registration_status' => 'approved',
                'approved' => true,
                'status' => 'active',
                'is_primary' => true,
                'profile_type' => 'professional',
                'profile_name' => 'Sophie Wilson - Sustainability Consultant'
            ]
        );
    }
} 