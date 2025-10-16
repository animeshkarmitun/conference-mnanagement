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
            // Tech Industry Professionals
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@techcorp.com',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'TechCorp Solutions',
                'bio' => 'Senior Software Engineer with expertise in cloud computing and DevOps practices.'
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'sarah.williams@ai-institute.org',
                'participant_type' => 'speaker',
                'travel_intent' => false,
                'registration_status' => 'approved',
                'organization' => 'AI Research Institute',
                'bio' => 'Leading researcher in machine learning and artificial intelligence applications.'
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@startup.io',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'pending',
                'organization' => 'Innovation Startup',
                'bio' => 'Entrepreneur and startup founder focused on sustainable technology solutions.'
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@university.edu',
                'participant_type' => 'speaker',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'State University',
                'bio' => 'Professor of Computer Science specializing in cybersecurity and data privacy.'
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Miller',
                'email' => 'robert.miller@events.com',
                'participant_type' => 'organizer',
                'travel_intent' => false,
                'registration_status' => 'approved',
                'organization' => 'CGS Events',
                'bio' => 'Event coordinator with 10+ years of experience in conference management.'
            ],
            [
                'first_name' => 'Lisa',
                'last_name' => 'Garcia',
                'email' => 'lisa.garcia@consulting.com',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'pending',
                'organization' => 'Digital Consulting Group',
                'bio' => 'Business consultant specializing in digital transformation and process optimization.'
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Martinez',
                'email' => 'james.martinez@fintech.com',
                'participant_type' => 'speaker',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'FinTech Innovations',
                'bio' => 'Financial technology expert with expertise in blockchain and digital payments.'
            ],
            [
                'first_name' => 'Jennifer',
                'last_name' => 'Anderson',
                'email' => 'jennifer.anderson@healthcare.org',
                'participant_type' => 'attendee',
                'travel_intent' => false,
                'registration_status' => 'approved',
                'organization' => 'Healthcare Solutions Inc.',
                'bio' => 'Healthcare IT specialist focused on telemedicine and patient data management.'
            ],
            [
                'first_name' => 'Christopher',
                'last_name' => 'Taylor',
                'email' => 'christopher.taylor@sustainability.org',
                'participant_type' => 'organizer',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'Green Future Initiative',
                'bio' => 'Environmental consultant and sustainability advocate with expertise in green technology.'
            ],
            [
                'first_name' => 'Amanda',
                'last_name' => 'Thomas',
                'email' => 'amanda.thomas@education.edu',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'pending',
                'organization' => 'Educational Technology Center',
                'bio' => 'EdTech specialist developing innovative learning platforms and digital tools.'
            ],
            [
                'first_name' => 'Daniel',
                'last_name' => 'Hernandez',
                'email' => 'daniel.hernandez@research.org',
                'participant_type' => 'speaker',
                'travel_intent' => false,
                'registration_status' => 'approved',
                'organization' => 'Climate Research Institute',
                'bio' => 'Climate scientist and researcher focusing on sustainable energy solutions.'
            ],
            [
                'first_name' => 'Michelle',
                'last_name' => 'Moore',
                'email' => 'michelle.moore@marketing.com',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'Digital Marketing Agency',
                'bio' => 'Marketing strategist with expertise in social media and content marketing.'
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Jackson',
                'email' => 'kevin.jackson@security.com',
                'participant_type' => 'organizer',
                'travel_intent' => false,
                'registration_status' => 'approved',
                'organization' => 'Cybersecurity Solutions',
                'bio' => 'Cybersecurity expert and consultant with 15+ years in information security.'
            ],
            [
                'first_name' => 'Stephanie',
                'last_name' => 'Martin',
                'email' => 'stephanie.martin@innovation.com',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'pending',
                'organization' => 'Innovation Labs',
                'bio' => 'Product manager and innovation strategist specializing in emerging technologies.'
            ],
            [
                'first_name' => 'Andrew',
                'last_name' => 'Lee',
                'email' => 'andrew.lee@data-science.com',
                'participant_type' => 'speaker',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'Data Science Corp',
                'bio' => 'Data scientist and analytics expert with expertise in big data and machine learning.'
            ],
            // Additional diverse participants
            [
                'first_name' => 'Maria',
                'last_name' => 'Rodriguez',
                'email' => 'maria.rodriguez@startup.co',
                'participant_type' => 'attendee',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'Tech Startup Inc.',
                'bio' => 'Frontend developer and UI/UX designer creating innovative user experiences.'
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Hassan',
                'email' => 'ahmed.hassan@research.edu',
                'participant_type' => 'speaker',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'Research University',
                'bio' => 'Professor of Artificial Intelligence and machine learning researcher.'
            ],
            [
                'first_name' => 'Yuki',
                'last_name' => 'Tanaka',
                'email' => 'yuki.tanaka@robotics.com',
                'participant_type' => 'attendee',
                'travel_intent' => false,
                'registration_status' => 'pending',
                'organization' => 'Robotics Solutions',
                'bio' => 'Robotics engineer developing autonomous systems and AI-powered robots.'
            ],
            [
                'first_name' => 'Priya',
                'last_name' => 'Patel',
                'email' => 'priya.patel@biotech.com',
                'participant_type' => 'speaker',
                'travel_intent' => true,
                'registration_status' => 'approved',
                'organization' => 'BioTech Innovations',
                'bio' => 'Biotechnology researcher focusing on AI applications in medical diagnostics.'
            ],
            [
                'first_name' => 'Marcus',
                'last_name' => 'Johnson',
                'email' => 'marcus.johnson@venture.com',
                'participant_type' => 'organizer',
                'travel_intent' => false,
                'registration_status' => 'approved',
                'organization' => 'Venture Capital Partners',
                'bio' => 'Venture capitalist and startup investor with focus on technology and innovation.'
            ],
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
                    'travel_intent' => $participantData['travel_intent'],
                    'registration_status' => $participantData['registration_status'],
                    'bio' => $participantData['bio'] ?? null,
                    'organization' => $participantData['organization'] ?? null,
                    'approved' => $participantData['registration_status'] === 'approved',
                    'status' => 'active',
                    'is_primary' => true,
                    'profile_type' => $participantData['participant_type'] === 'speaker' ? 'speaker' : 'professional',
                    'profile_name' => $participantData['first_name'] . ' ' . $participantData['last_name'] . ' - ' . ($participantData['organization'] ?? 'Professional'),
                ]
            );
        }

        $this->command->info('Additional participants seeded successfully!');
    }
}
