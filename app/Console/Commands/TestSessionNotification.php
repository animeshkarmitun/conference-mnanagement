<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Session;
use App\Models\Conference;
use App\Services\SessionNotificationService;
use App\Services\ConferenceNotificationService;

class TestSessionNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:session-notification {--session-id=1} {--conference-id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test session and conference notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Session and Conference Notification System...');
        
        // Test session notification
        $sessionId = $this->option('session-id');
        $session = Session::find($sessionId);
        
        if (!$session) {
            $this->error("Session with ID {$sessionId} not found!");
            return 1;
        }
        
        $this->info("Testing session: {$session->title}");
        
        $sessionNotificationService = new SessionNotificationService();
        
        // Test session date update notification
        $oldData = [
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
        ];
        
        $newData = [
            'start_time' => now()->addDays(1)->addHours(2),
            'end_time' => now()->addDays(1)->addHours(4),
        ];
        
        $this->info('Triggering session date update notification...');
        $sessionNotificationService->notifySessionDatesUpdated($session, $oldData, $newData);
        
        // Test conference notification
        $conferenceId = $this->option('conference-id');
        $conference = Conference::find($conferenceId);
        
        if (!$conference) {
            $this->error("Conference with ID {$conferenceId} not found!");
            return 1;
        }
        
        $this->info("Testing conference: {$conference->name}");
        
        $conferenceNotificationService = new ConferenceNotificationService();
        
        // Test conference date update notification
        $oldConferenceData = [
            'start_date' => $conference->start_date,
            'end_date' => $conference->end_date,
        ];
        
        $newConferenceData = [
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
        ];
        
        $this->info('Triggering conference date update notification...');
        $conferenceNotificationService->notifyConferenceDatesUpdated($conference, $oldConferenceData, $newConferenceData);
        
        $this->info('Notification tests completed! Check the logs and database for results.');
        
        return 0;
    }
}
