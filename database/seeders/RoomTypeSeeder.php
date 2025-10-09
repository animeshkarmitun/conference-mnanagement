<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Standard',
                'description' => 'Comfortable standard room with basic amenities',
                'default_beds' => 1,
                'base_price' => 89.99,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Work Desk'],
                'is_active' => true,
            ],
            [
                'name' => 'Deluxe',
                'description' => 'Spacious deluxe room with enhanced amenities',
                'default_beds' => 2,
                'base_price' => 129.99,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Work Desk', 'Mini Bar', 'Coffee Maker'],
                'is_active' => true,
            ],
            [
                'name' => 'Suite',
                'description' => 'Luxurious suite with separate living area',
                'default_beds' => 2,
                'base_price' => 199.99,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Work Desk', 'Mini Bar', 'Coffee Maker', 'Sofa', 'Balcony'],
                'is_active' => true,
            ],
            [
                'name' => 'Executive',
                'description' => 'Executive room with business amenities',
                'default_beds' => 1,
                'base_price' => 159.99,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Work Desk', 'Mini Bar', 'Coffee Maker', 'Printer Access'],
                'is_active' => true,
            ],
            [
                'name' => 'Presidential',
                'description' => 'Ultimate luxury presidential suite',
                'default_beds' => 2,
                'base_price' => 399.99,
                'amenities' => ['WiFi', 'TV', 'Air Conditioning', 'Work Desk', 'Mini Bar', 'Coffee Maker', 'Sofa', 'Balcony', 'Jacuzzi', 'Butler Service'],
                'is_active' => true,
            ],
        ];

        foreach ($roomTypes as $roomType) {
            \App\Models\RoomType::create($roomType);
        }
    }
}
