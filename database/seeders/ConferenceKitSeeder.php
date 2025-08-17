<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConferenceKit;
use App\Models\ConferenceKitItem;
use App\Models\Conference;

class ConferenceKitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first conference
        $conference = Conference::first();
        
        if (!$conference) {
            $this->command->info('No conferences found. Please run ConferenceSeeder first.');
            return;
        }

        // Create conference kit
        $conferenceKit = ConferenceKit::firstOrCreate([
            'conference_id' => $conference->id,
        ]);

        // Create session links
        $sessionLinks = [
            [
                'title' => 'Opening Keynote: Future of Technology',
                'time' => '9:00 AM - 10:30 AM',
                'room' => 'Main Hall',
                'speaker' => 'Dr. Sarah Johnson',
                'description' => 'An inspiring keynote address exploring the future of technology and its impact on society.',
                'zoom_link' => 'https://zoom.us/j/123456789?pwd=abcdef123456'
            ],
            [
                'title' => 'AI and Machine Learning Workshop',
                'time' => '11:00 AM - 12:30 PM',
                'room' => 'Workshop Room A',
                'speaker' => 'Michael Chen',
                'description' => 'Hands-on workshop covering the latest developments in AI and machine learning.',
                'zoom_link' => 'https://zoom.us/j/987654321?pwd=xyz789def'
            ],
            [
                'title' => 'Digital Transformation Panel',
                'time' => '2:00 PM - 3:30 PM',
                'room' => 'Conference Room B',
                'speaker' => 'Panel Discussion',
                'description' => 'Industry experts discuss digital transformation strategies and best practices.',
                'zoom_link' => 'https://zoom.us/j/456789123?pwd=ghi456jkl'
            ],
            [
                'title' => 'Networking Session',
                'time' => '4:00 PM - 5:30 PM',
                'room' => 'Networking Lounge',
                'speaker' => 'All Participants',
                'description' => 'Open networking session to connect with fellow participants and speakers.',
                'zoom_link' => 'https://zoom.us/j/789123456?pwd=mno789pqr'
            ]
        ];

        foreach ($sessionLinks as $sessionLink) {
            ConferenceKitItem::firstOrCreate([
                'kit_id' => $conferenceKit->id,
                'type' => 'SessionLink',
                'content' => json_encode($sessionLink),
            ]);
        }

        // Create contacts
        $contacts = [
            [
                'name' => 'Conference Support Team',
                'email' => 'support@conference.com',
                'phone' => '+1-555-0123',
                'role' => 'Technical Support',
                'availability' => '24/7 during conference'
            ],
            [
                'name' => 'Event Coordinator',
                'email' => 'coordinator@conference.com',
                'phone' => '+1-555-0124',
                'role' => 'Event Management',
                'availability' => '8:00 AM - 6:00 PM'
            ],
            [
                'name' => 'Registration Desk',
                'email' => 'registration@conference.com',
                'phone' => '+1-555-0125',
                'role' => 'Registration Support',
                'availability' => '7:00 AM - 7:00 PM'
            ]
        ];

        foreach ($contacts as $contact) {
            ConferenceKitItem::firstOrCreate([
                'kit_id' => $conferenceKit->id,
                'type' => 'Contact',
                'content' => json_encode($contact),
            ]);
        }

        // Create city guide
        $cityGuide = [
            'title' => 'Welcome to New York City',
            'description' => 'Welcome to the Big Apple! This guide provides essential information to help you make the most of your conference stay in New York City.',
            'transportation' => 'Subway: Lines A, C, E, 1, 2, 3 serve the conference area. Taxi services available 24/7. Uber and Lyft are widely available.',
            'restaurants' => 'Nearby options: Conference Center Café (0.1 miles), Downtown Bistro (0.3 miles), Tech District Grill (0.2 miles). Many options within walking distance.',
            'attractions' => 'Times Square (2 miles), Central Park (3 miles), Empire State Building (1.5 miles), Broadway theaters (1 mile).',
            'emergency_contacts' => 'Emergency: 911, Conference Security: +1-555-9999, Local Police: +1-555-8888, Medical Emergency: +1-555-7777'
        ];

        ConferenceKitItem::firstOrCreate([
            'kit_id' => $conferenceKit->id,
            'type' => 'CityGuide',
            'content' => json_encode($cityGuide),
        ]);

        $this->command->info('Conference kit seeded successfully for conference: ' . $conference->name);
    }
}
