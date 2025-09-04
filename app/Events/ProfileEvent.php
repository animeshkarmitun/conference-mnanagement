<?php

namespace App\Events;

use App\Models\Participant;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $participant;
    public $eventType;
    public $message;
    public $changes;
    public $conferenceId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Participant $participant,
        string $eventType,
        string $message,
        array $changes = []
    ) {
        $this->participant = $participant;
        $this->eventType = $eventType;
        $this->message = $message;
        $this->changes = $changes;
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
