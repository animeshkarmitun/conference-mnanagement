<?php

namespace Database\Seeders;

use App\Models\Conference;
use Illuminate\Database\Seeder;

class ConferenceSeeder extends Seeder
{
    public function run(): void
    {
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