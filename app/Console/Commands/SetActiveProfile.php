<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Participant;

class SetActiveProfile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:set-active {user_id} {participant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set active participant profile for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $participantId = $this->argument('participant_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $participant = $user->participants()->find($participantId);
        if (!$participant) {
            $this->error("Participant with ID {$participantId} not found for user {$userId}.");
            return 1;
        }

        // Set the active profile
        $user->setActiveParticipantProfile($participantId);

        $this->info("Set active profile for user {$userId} to participant {$participantId} ({$participant->conference->name}).");

        // Verify
        $activeProfile = $user->getActiveParticipantProfile();
        if ($activeProfile) {
            $this->info("Active profile is now: {$activeProfile->id} ({$activeProfile->conference->name})");
        } else {
            $this->error("Failed to set active profile.");
            return 1;
        }

        return 0;
    }
}