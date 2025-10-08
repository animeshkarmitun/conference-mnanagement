<?php

namespace App\Services;

use App\Events\TaskEvent;
use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskNotificationService
{
    /**
     * Send notification when a task is assigned
     */
    public function notifyTaskAssigned(Task $task, $user = null): void
    {
        if ($user) {
            // Single user assignment
            $message = "New task assigned: '{$task->title}' to {$user->first_name} {$user->last_name}";
        } else {
            // Multiple user assignment
            $userCount = $task->users->count();
            $message = "New task assigned: '{$task->title}' to {$userCount} user(s)";
        }
        
        if ($task->due_date) {
            $message .= " - Due: " . $task->due_date->format('M d, Y');
        }
        $message .= " - Priority: " . ucfirst($task->priority);

        $this->triggerTaskEvent($task, 'task_assigned', $message);
    }

    /**
     * Send notification when a task is updated
     */
    public function notifyTaskUpdated(Task $task): void
    {
        $userCount = $task->users->count();
        $message = "Task updated: '{$task->title}' assigned to {$userCount} user(s)";
        
        if ($task->due_date) {
            $message .= " - Due: " . $task->due_date->format('M d, Y');
        }
        $message .= " - Priority: " . ucfirst($task->priority);

        $this->triggerTaskEvent($task, 'task_updated', $message);
    }

    /**
     * Send notification when a task is completed
     */
    public function notifyTaskCompleted(Task $task): void
    {
        $userCount = $task->users->count();
        $message = "Task completed: '{$task->title}' by {$userCount} user(s)";

        $this->triggerTaskEvent($task, 'task_completed', $message);
    }

    /**
     * Send notification when task status changes
     */
    public function notifyTaskStatusChanged(Task $task, string $oldStatus): void
    {
        $newStatus = ucfirst(str_replace('_', ' ', $task->status));
        $oldStatusFormatted = ucfirst(str_replace('_', ' ', $oldStatus));
        
        $message = "Task status changed: '{$task->title}' from {$oldStatusFormatted} to {$newStatus}";

        $this->triggerTaskEvent($task, 'task_status_changed', $message);
    }

    /**
     * Send notification when a task is overdue
     */
    public function notifyTaskOverdue(Task $task): void
    {
        $userCount = $task->users->count();
        $message = "Task overdue: '{$task->title}' assigned to {$userCount} user(s) - Due: " . $task->due_date->format('M d, Y');

        $this->triggerTaskEvent($task, 'task_overdue', $message);
    }

    /**
     * Send notification when a task is due soon (within 24 hours)
     */
    public function notifyTaskDueSoon(Task $task): void
    {
        $userCount = $task->users->count();
        $message = "Task due soon: '{$task->title}' assigned to {$userCount} user(s) - Due: " . $task->due_date->format('M d, Y H:i');

        $this->triggerTaskEvent($task, 'task_due_soon', $message);
    }

    /**
     * Trigger task event
     */
    private function triggerTaskEvent(Task $task, string $eventType, string $message): void
    {
        try {
            $event = new TaskEvent($task, $eventType, $message);
            event($event);
            
            Log::info('Task event triggered', [
                'task_id' => $task->id,
                'event_type' => $eventType,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to trigger task event', [
                'error' => $e->getMessage(),
                'task_id' => $task->id,
                'event_type' => $eventType
            ]);
        }
    }
}
