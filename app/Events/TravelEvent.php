<?php

namespace App\Events;

use App\Models\Participant;
use App\Models\TravelDetail;
use App\Models\RoomAllocation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TravelEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $participant;
    public $travelDetail;
    public $roomAllocation;
    public $eventType;
    public $message;
    public $conferenceId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Participant $participant,
        string $eventType,
        string $message,
        ?TravelDetail $travelDetail = null,
        ?RoomAllocation $roomAllocation = null
    ) {
        $this->participant = $participant;
        $this->eventType = $eventType;
        $this->message = $message;
        $this->travelDetail = $travelDetail;
        $this->roomAllocation = $roomAllocation;
        $this->conferenceId = $participant->conference_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
