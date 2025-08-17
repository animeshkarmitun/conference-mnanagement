<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Session;
use App\Models\Conference;
use App\Models\Participant;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Venue;
use App\Models\ParticipantType;
use App\Services\SessionNotificationService;
use App\Services\ConferenceNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SessionConferenceNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $superadminRole = Role::create(['name' => 'superadmin', 'permissions' => ['*']]);
        $attendeeRole = Role::create(['name' => 'attendee', 'permissions' => ['sessions.view']]);
        
        // Create participant type
        $participantType = ParticipantType::create(['name' => 'attendee']);
        
        // Create venue
        $venue = Venue::create([
            'name' => 'Test Venue',
            'address' => 'Test Address',
            'capacity' => 100,
        ]);
        
        // Create superadmin user
        $superadmin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
        ]);
        $superadmin->roles()->attach($superadminRole->id);
        
        // Create attendee user
        $attendee = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);
        $attendee->roles()->attach($attendeeRole->id);
        
        // Create conference
        $conference = Conference::create([
            'name' => 'Test Conference',
            'start_date' => '2025-08-20',
            'end_date' => '2025-08-22',
            'location' => 'Test Location',
            'venue_id' => $venue->id,
        ]);
        
        // Create participant
        Participant::create([
            'user_id' => $attendee->id,
            'conference_id' => $conference->id,
            'participant_type_id' => $participantType->id,
            'registration_status' => 'approved',
        ]);
        
        // Create session
        $session = Session::create([
            'conference_id' => $conference->id,
            'venue_id' => $venue->id,
            'title' => 'Test Session',
            'description' => 'Test session description',
            'start_time' => '2025-08-20 10:00:00',
            'end_time' => '2025-08-20 11:00:00',
            'room' => 'Room A',
        ]);
        
        // Assign participant to session
        $participant = Participant::where('user_id', $attendee->id)->first();
        $session->participants()->attach($participant->id);
    }

    /** @test */
    public function session_date_change_triggers_notification_for_participants()
    {
        $session = Session::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $oldData = [
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
        ];
        
        $newData = [
            'start_time' => '2025-08-21 14:00:00',
            'end_time' => '2025-08-21 15:00:00',
        ];
        
        $sessionNotificationService = new SessionNotificationService();
        $sessionNotificationService->notifySessionDatesUpdated($session, $oldData, $newData);
        
        // Check if notification was created
        $notification = Notification::where('user_id', $attendee->id)
            ->where('type', 'SessionUpdate')
            ->first();
            
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Test Session', $notification->message);
        $this->assertStringContainsString('schedule updated', $notification->message);
        $this->assertEquals($session->id, $notification->related_id);
    }

    /** @test */
    public function conference_date_change_triggers_notification_for_participants()
    {
        $conference = Conference::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $oldData = [
            'start_date' => $conference->start_date,
            'end_date' => $conference->end_date,
        ];
        
        $newData = [
            'start_date' => '2025-09-15',
            'end_date' => '2025-09-17',
        ];
        
        $conferenceNotificationService = new ConferenceNotificationService();
        $conferenceNotificationService->notifyConferenceDatesUpdated($conference, $oldData, $newData);
        
        // Check if notification was created
        $notification = Notification::where('user_id', $attendee->id)
            ->where('type', 'ConferenceUpdate')
            ->first();
            
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Test Conference', $notification->message);
        $this->assertStringContainsString('schedule updated', $notification->message);
        $this->assertEquals($conference->id, $notification->related_id);
    }

    /** @test */
    public function session_venue_change_triggers_notification()
    {
        $session = Session::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $sessionNotificationService = new SessionNotificationService();
        $sessionNotificationService->notifySessionVenueUpdated($session, 'Room A', 'Room B');
        
        // Check if notification was created
        $notification = Notification::where('user_id', $attendee->id)
            ->where('type', 'SessionUpdate')
            ->first();
            
        $this->assertNotNull($notification);
        $this->assertStringContainsString('venue updated', $notification->message);
        $this->assertStringContainsString('Room A', $notification->message);
        $this->assertStringContainsString('Room B', $notification->message);
    }

    /** @test */
    public function session_cancellation_triggers_notification()
    {
        $session = Session::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $sessionNotificationService = new SessionNotificationService();
        $sessionNotificationService->notifySessionCancelled($session);
        
        // Check if notification was created
        $notification = Notification::where('user_id', $attendee->id)
            ->where('type', 'SessionUpdate')
            ->first();
            
        $this->assertNotNull($notification);
        $this->assertStringContainsString('has been cancelled', $notification->message);
    }

    /** @test */
    public function conference_cancellation_triggers_notification()
    {
        $conference = Conference::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $conferenceNotificationService = new ConferenceNotificationService();
        $conferenceNotificationService->notifyConferenceCancelled($conference);
        
        // Check if notification was created
        $notification = Notification::where('user_id', $attendee->id)
            ->where('type', 'ConferenceUpdate')
            ->first();
            
        $this->assertNotNull($notification);
        $this->assertStringContainsString('has been cancelled', $notification->message);
    }

    /** @test */
    public function notifications_are_not_duplicated_within_five_minutes()
    {
        $session = Session::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $oldData = [
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
        ];
        
        $newData = [
            'start_time' => '2025-08-21 14:00:00',
            'end_time' => '2025-08-21 15:00:00',
        ];
        
        $sessionNotificationService = new SessionNotificationService();
        
        // Trigger notification twice
        $sessionNotificationService->notifySessionDatesUpdated($session, $oldData, $newData);
        $sessionNotificationService->notifySessionDatesUpdated($session, $oldData, $newData);
        
        // Should only have one notification
        $notificationCount = Notification::where('user_id', $attendee->id)
            ->where('type', 'SessionUpdate')
            ->count();
            
        $this->assertEquals(1, $notificationCount);
    }

    /** @test */
    public function notification_contains_correct_action_url()
    {
        $session = Session::first();
        $attendee = User::where('email', 'john@example.com')->first();
        
        $oldData = [
            'start_time' => $session->start_time,
            'end_time' => $session->end_time,
        ];
        
        $newData = [
            'start_time' => '2025-08-21 14:00:00',
            'end_time' => '2025-08-21 15:00:00',
        ];
        
        $sessionNotificationService = new SessionNotificationService();
        $sessionNotificationService->notifySessionDatesUpdated($session, $oldData, $newData);
        
        $notification = Notification::where('user_id', $attendee->id)
            ->where('type', 'SessionUpdate')
            ->first();
            
        $this->assertNotNull($notification);
        $this->assertStringContainsString('/sessions/' . $session->id, $notification->action_url);
    }
}
