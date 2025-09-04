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
            ['name' => 'Digital Marketing Summit 2024'],
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
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(62),
                'location' => 'Conference Hall A',
                'status' => 'planned',
                'venue_id' => 2
            ]
        );
    }
} 