<?php

namespace App\Console\Commands;

use App\Models\Participant;
use App\Models\TravelDetail;
use App\Models\RoomAllocation;
use App\Models\Hotel;
use App\Services\TravelNotificationService;
use Illuminate\Console\Command;

class TestTravelNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel:test-notifications {--type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test travel notifications for different scenarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $travelNotificationService = new TravelNotificationService();

        $this->info('Testing Travel Notifications...');
        $this->newLine();

        // Get a sample participant
        $participant = Participant::with(['user', 'conference'])->first();
        
        if (!$participant) {
            $this->error('No participants found. Please create a participant first.');
            return 1;
        }

        $this->info("Using participant: {$participant->user->first_name} {$participant->user->last_name}");
        $this->newLine();

        switch ($type) {
            case 'travel_details':
                $this->testTravelDetailsUpdate($participant, $travelNotificationService);
                break;
            case 'room_allocation':
                $this->testRoomAllocation($participant, $travelNotificationService);
                break;
            case 'travel_documents':
                $this->testTravelDocuments($participant, $travelNotificationService);
                break;
            case 'conflicts':
                $this->testTravelConflicts($participant, $travelNotificationService);
                break;
            case 'all':
            default:
                $this->testTravelDetailsUpdate($participant, $travelNotificationService);
                $this->testRoomAllocation($participant, $travelNotificationService);
                $this->testTravelDocuments($participant, $travelNotificationService);
                $this->testTravelConflicts($participant, $travelNotificationService);
                break;
        }

        $this->info('Travel notification tests completed!');
        $this->info('Check the logs and notifications table for results.');
        
        return 0;
    }

    private function testTravelDetailsUpdate(Participant $participant, TravelNotificationService $service)
    {
        $this->info('Testing Travel Details Update Notification...');
        
        // Create or get travel detail
        $travelDetail = $participant->travelDetails ?: new TravelDetail();
        $travelDetail->participant_id = $participant->id;
        $travelDetail->arrival_date = now()->addDays(7);
        $travelDetail->departure_date = now()->addDays(10);
        $travelDetail->flight_info = 'Test Flight Info';
        
        // Get a hotel
        $hotel = Hotel::first();
        if ($hotel) {
            $travelDetail->hotel_id = $hotel->id;
        }
        
        $service->notifyTravelDetailsUpdated($participant, $travelDetail);
        
        $this->info('✓ Travel details update notification sent');
        $this->newLine();
    }

    private function testRoomAllocation(Participant $participant, TravelNotificationService $service)
    {
        $this->info('Testing Room Allocation Notification...');
        
        // Create a room allocation
        $roomAllocation = new RoomAllocation();
        $roomAllocation->participant_id = $participant->id;
        $roomAllocation->room_number = '101';
        $roomAllocation->check_in = now()->addDays(7)->setTime(14, 0);
        $roomAllocation->check_out = now()->addDays(10)->setTime(11, 0);
        
        // Get a hotel
        $hotel = Hotel::first();
        if ($hotel) {
            $roomAllocation->hotel_id = $hotel->id;
            $service->notifyRoomAllocated($participant, $roomAllocation);
            $this->info('✓ Room allocation notification sent');
        } else {
            $this->warn('No hotels found. Skipping room allocation test.');
        }
        
        $this->newLine();
    }

    private function testTravelDocuments(Participant $participant, TravelNotificationService $service)
    {
        $this->info('Testing Travel Documents Upload Notification...');
        
        $service->notifyTravelDocumentsUploaded($participant);
        
        $this->info('✓ Travel documents upload notification sent');
        $this->newLine();
    }

    private function testTravelConflicts(Participant $participant, TravelNotificationService $service)
    {
        $this->info('Testing Travel Conflict Notification...');
        
        $conflictDetails = 'Test conflict: Room 101 is double-booked for overlapping dates';
        $service->notifyTravelConflict($participant, $conflictDetails);
        
        $this->info('✓ Travel conflict notification sent');
        $this->newLine();
    }
}
