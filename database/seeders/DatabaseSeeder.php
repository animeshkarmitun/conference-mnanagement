<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            VenueSeeder::class,
            ConferenceSeeder::class,
            SessionSeeder::class,
            ParticipantTypeSeeder::class,
            ParticipantSeeder::class,
            AdditionalParticipantsSeeder::class,
            TaskerSeeder::class,
            ConferenceTasksSeeder::class,
            TaskerSpecificSeeder::class,
            TaskerNotificationSeeder::class,
            UpcomingConferenceSessionsSeeder::class,
            ConferenceKitSeeder::class,
            IdCardSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
