<?php

namespace App\Events;

use App\Models\Conference;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConferenceEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conference;
    public $eventType;
    public $message;
    public $changes;
    public $conferenceId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Conference $conference,
        string $eventType,
        string $message,
        array $changes = []
    ) {
        $this->conference = $conference;
        $this->eventType = $eventType;
        $this->message = $message;
        $this->changes = $changes;
        $this->conferenceId = $conference->id;
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
