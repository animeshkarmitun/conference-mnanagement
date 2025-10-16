<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Conference;
use App\Services\PasswordlessLoginService;

class GeneratePasswordlessLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwordless:generate {user_id} {conference_id} {--expiration=24}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate passwordless login link for a user and conference';

    protected $passwordlessLoginService;

    public function __construct(PasswordlessLoginService $passwordlessLoginService)
    {
        parent::__construct();
        $this->passwordlessLoginService = $passwordlessLoginService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $conferenceId = $this->argument('conference_id');
        $expirationHours = $this->option('expiration');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $conference = Conference::find($conferenceId);
        if (!$conference) {
            $this->error("Conference with ID {$conferenceId} not found.");
            return 1;
        }

        // Check if user has a participant profile for this conference
        $participant = $user->participants()
            ->where('conference_id', $conferenceId)
            ->where('status', 'active')
            ->first();

        if (!$participant) {
            $this->error("User {$userId} does not have an active participant profile for conference {$conferenceId} ({$conference->name}).");
            return 1;
        }

        try {
            // Generate passwordless login link
            $passwordlessLogin = $this->passwordlessLoginService->generateLoginLink($user, $expirationHours, $conference);
            
            // Send email
            $emailSent = $this->passwordlessLoginService->sendLoginEmail($user, $passwordlessLogin, $conference);

            $this->info("Generated passwordless login link for user {$userId} ({$user->first_name} {$user->last_name})");
            $this->info("Conference: {$conference->name}");
            $this->info("Participant: {$participant->id} ({$participant->participantType->name})");
            $this->info("Login URL: {$passwordlessLogin->getLoginUrl()}");
            $this->info("Expires at: {$passwordlessLogin->expires_at}");
            $this->info("Email sent: " . ($emailSent ? 'Yes' : 'No'));

        } catch (\Exception $e) {
            $this->error("Failed to generate passwordless login: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}