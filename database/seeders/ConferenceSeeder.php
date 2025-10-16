<?php

namespace Database\Seeders;

use App\Models\Conference;
use Illuminate\Database\Seeder;

class ConferenceSeeder extends Seeder
{
    public function run(): void
    {
        // Active conferences (ongoing)
        Conference::firstOrCreate(
            ['name' => 'Digital Marketing Summit 2025'],
            [
                'description' => 'The premier event for digital marketing professionals to share insights and strategies.',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(3),
                'location' => 'Main Convention Center',
                'status' => 'ongoing',
                'venue_id' => 1
            ]
        );
        
        Conference::firstOrCreate(
            ['name' => 'Healthcare Innovation Conference'],
            [
                'description' => 'Advancing healthcare through technology and innovation.',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(4),
                'location' => 'Medical Center Auditorium',
                'status' => 'ongoing',
                'venue_id' => 2
            ]
        );

        // Conferences starting in 2 days
        Conference::firstOrCreate(
            ['name' => 'AI & Machine Learning Expo'],
            [
                'description' => 'Exploring the latest developments in artificial intelligence and machine learning.',
                'start_date' => now()->addDays(2),
                'end_date' => now()->addDays(4),
                'location' => 'Tech Innovation Hub',
                'status' => 'planned',
                'venue_id' => 1
            ]
        );
        
        Conference::firstOrCreate(
            ['name' => 'Sustainable Business Conference'],
            [
                'description' => 'Building sustainable business practices for the future.',
                'start_date' => now()->addDays(2),
                'end_date' => now()->addDays(5),
                'location' => 'Green Business Center',
                'status' => 'planned',
                'venue_id' => 2
            ]
        );

        // Additional conferences with more variety
        Conference::firstOrCreate(
            ['name' => 'FinTech Revolution Summit'],
            [
                'description' => 'Exploring the future of financial technology and digital banking.',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(9),
                'location' => 'Financial District Center',
                'status' => 'planned',
                'venue_id' => 3
            ]
        );

        Conference::firstOrCreate(
            ['name' => 'Climate Change & Sustainability Forum'],
            [
                'description' => 'Addressing climate challenges through innovative solutions.',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(16),
                'location' => 'Environmental Research Center',
                'status' => 'planned',
                'venue_id' => 1
            ]
        );

        Conference::firstOrCreate(
            ['name' => 'EdTech Innovation Conference'],
            [
                'description' => 'Revolutionizing education through technology and digital learning.',
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(23),
                'location' => 'University Conference Center',
                'status' => 'planned',
                'venue_id' => 2
            ]
        );

        // Existing planned conferences (further in the future)
        Conference::firstOrCreate(
            ['name' => 'Annual Tech Summit'],
            [
                'description' => 'A gathering of tech enthusiasts and professionals.',
                'start_date' => now()->addDays(30),
                'end_date' => now()->addDays(32),
                'location' => 'Grand Ballroom',
                'status' => 'planned',
                'venue_id' => 1
            ]
        );
        
        Conference::firstOrCreate(
            ['name' => 'Business Innovation Forum'],
            [
                'description' => 'Exploring new business strategies and innovations.',
                'start_date' => now()->addDays(45),
                'end_date' => now()->addDays(47),
                'location' => 'Conference Hall A',
                'status' => 'planned',
                'venue_id' => 2
            ]
        );

        Conference::firstOrCreate(
            ['name' => 'Cybersecurity & Privacy Summit'],
            [
                'description' => 'Protecting digital assets and ensuring privacy in the modern world.',
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(62),
                'location' => 'Security Institute',
                'status' => 'planned',
                'venue_id' => 3
            ]
        );

        Conference::firstOrCreate(
            ['name' => 'Startup Ecosystem Conference'],
            [
                'description' => 'Connecting entrepreneurs, investors, and innovators.',
                'start_date' => now()->addDays(75),
                'end_date' => now()->addDays(77),
                'location' => 'Innovation Hub',
                'status' => 'planned',
                'venue_id' => 1
            ]
        );

        // Past conferences for historical data
        Conference::firstOrCreate(
            ['name' => 'Global Leadership Summit 2024'],
            [
                'description' => 'Developing leadership skills for the modern workplace.',
                'start_date' => now()->subDays(90),
                'end_date' => now()->subDays(88),
                'location' => 'Leadership Center',
                'status' => 'completed',
                'venue_id' => 2
            ]
        );

        Conference::firstOrCreate(
            ['name' => 'Data Science & Analytics Conference'],
            [
                'description' => 'Exploring big data and advanced analytics techniques.',
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(58),
                'location' => 'Data Center Auditorium',
                'status' => 'completed',
                'venue_id' => 3
            ]
        );
    }
} 