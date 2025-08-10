<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $eventType;
    public $message;
    public $conferenceId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Task $task,
        string $eventType,
        string $message
    ) {
        $this->task = $task;
        $this->eventType = $eventType;
        $this->message = $message;
        $this->conferenceId = $task->conference_id;
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
