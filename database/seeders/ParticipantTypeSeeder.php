<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('participant_types')->insert([
            ['name' => 'attendee'],
            ['name' => 'speaker'],
            ['name' => 'organizer'],
        ]);
    }
}
