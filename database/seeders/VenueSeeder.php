<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        Venue::firstOrCreate(
            ['name' => 'Grand Ballroom'],
            [
                'address' => '123 Main St, Downtown District, City Center',
                'capacity' => 500
            ]
        );
        
        Venue::firstOrCreate(
            ['name' => 'Conference Hall A'],
            [
                'address' => '456 Oak Ave, Business District, Town',
                'capacity' => 300
            ]
        );
        
        Venue::firstOrCreate(
            ['name' => 'Exhibition Center'],
            [
                'address' => '789 Pine Rd, Industrial Zone, Village',
                'capacity' => 1000
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Tech Innovation Hub'],
            [
                'address' => '321 Silicon Valley Blvd, Tech Quarter, Innovation City',
                'capacity' => 400
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Medical Center Auditorium'],
            [
                'address' => '654 Healthcare Ave, Medical District, Health City',
                'capacity' => 250
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Green Business Center'],
            [
                'address' => '987 Eco Park Dr, Sustainable District, Green City',
                'capacity' => 350
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Financial District Center'],
            [
                'address' => '147 Wall Street Plaza, Financial District, Metro City',
                'capacity' => 600
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Environmental Research Center'],
            [
                'address' => '258 Nature Trail, Research Campus, Eco City',
                'capacity' => 200
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'University Conference Center'],
            [
                'address' => '369 Campus Blvd, University District, Education City',
                'capacity' => 450
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Security Institute'],
            [
                'address' => '741 Secure Lane, Government Quarter, Capital City',
                'capacity' => 300
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Innovation Hub'],
            [
                'address' => '852 Startup Street, Innovation District, Tech City',
                'capacity' => 400
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Leadership Center'],
            [
                'address' => '963 Executive Blvd, Corporate District, Business City',
                'capacity' => 350
            ]
        );

        Venue::firstOrCreate(
            ['name' => 'Data Center Auditorium'],
            [
                'address' => '159 Algorithm Ave, Tech Campus, Data City',
                'capacity' => 500
            ]
        );
    }
} 