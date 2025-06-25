<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ParticipantType;

class ParticipantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['attendee', 'speaker', 'organizer'];
        
        foreach ($types as $type) {
            ParticipantType::firstOrCreate(['name' => $type]);
        }
    }
}
