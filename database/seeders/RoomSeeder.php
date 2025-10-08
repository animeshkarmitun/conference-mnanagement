<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = \App\Models\Hotel::all();
        
        foreach ($hotels as $hotel) {
            // Create 10-15 rooms per hotel
            $roomCount = rand(10, 15);
            
            for ($i = 1; $i <= $roomCount; $i++) {
                $roomTypes = ['Standard', 'Deluxe', 'Suite', 'Executive', 'Presidential'];
                $roomType = $roomTypes[array_rand($roomTypes)];
                $beds = rand(1, 4);
                
                \App\Models\Room::create([
                    'hotel_id' => $hotel->id,
                    'room_number' => str_pad($i, 3, '0', STR_PAD_LEFT),
                    'room_type' => $roomType,
                    'beds' => $beds,
                    'price_per_night' => rand(50, 500),
                    'description' => "Comfortable {$roomType} room with {$beds} bed" . ($beds > 1 ? 's' : ''),
                    'is_available' => true,
                ]);
            }
        }
    }
}
