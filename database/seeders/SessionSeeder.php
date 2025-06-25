<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        Session::firstOrCreate(
            ['title' => 'Keynote Speech', 'conference_id' => 1],
            [
                'description' => 'Opening keynote by industry leaders.',
                'start_time' => now()->addDays(30)->setTime(9, 0),
                'end_time' => now()->addDays(30)->setTime(10, 30),
                'venue_id' => 1,
            ]
        );
        
        Session::firstOrCreate(
            ['title' => 'Panel Discussion', 'conference_id' => 1],
            [
                'description' => 'Interactive panel on emerging technologies.',
                'start_time' => now()->addDays(30)->setTime(11, 0),
                'end_time' => now()->addDays(30)->setTime(12, 30),
                'venue_id' => 1,
            ]
        );
        
        Session::firstOrCreate(
            ['title' => 'Business Strategies Workshop', 'conference_id' => 2],
            [
                'description' => 'Workshop on innovative business strategies.',
                'start_time' => now()->addDays(60)->setTime(10, 0),
                'end_time' => now()->addDays(60)->setTime(12, 0),
                'venue_id' => 2,
            ]
        );
    }
} 