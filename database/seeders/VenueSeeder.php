<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        Venue::create([
            'name' => 'Grand Ballroom',
            'address' => '123 Main St, City',
            'capacity' => 500
        ]);
        Venue::create([
            'name' => 'Conference Hall A',
            'address' => '456 Oak Ave, Town',
            'capacity' => 300
        ]);
        Venue::create([
            'name' => 'Exhibition Center',
            'address' => '789 Pine Rd, Village',
            'capacity' => 1000
        ]);
    }
} 