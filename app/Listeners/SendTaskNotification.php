<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use App\Services\NotificationTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTaskNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected NotificationTemplateService $notificationTemplateService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationTemplateService $notificationTemplateService)
    {
        $this->notificationTemplateService = $notificationTemplateService;
    }

    /**
     * Handle the event.
     */
    public function handle(TaskEvent $event): void
    {
        try {
            // Get users who should receive task notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->createNotification($user, $event);
            }

            Log::info('Task notification sent', [
                'event_type' => $event->eventType,
                'task_id' => $event->task->id,
                'users_notified' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send task notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'task_id' => $event->task->id
            ]);
        }
    }

    /**
     * Get users who should receive task notifications
     */
    private function getUsersToNotify(TaskEvent $event): array
    {
        $users = [];
        $currentUser = auth()->user();
        
        // Check if current user is admin or superadmin
        $isCurrentUserAdmin = $currentUser && $currentUser->roles()->whereIn('name', ['admin', 'superadmin'])->exists();
        
        if ($isCurrentUserAdmin) {
            // If admin/superadmin made the change, notify assigned users
            $assignedUsers = $event->task->users;
            foreach ($assignedUsers as $user) {
                // Don't notify the admin who made the change
                if ($user->id !== $currentUser->id) {
                    $users[] = $user;
                }
            }
            
            Log::info('Admin/Superadmin task change - notifying assigned users', [
                'admin_user' => $currentUser->email,
                'task_id' => $event->task->id,
                'assigned_users_count' => count($users)
            ]);
            
        } else {
            // If regular user/tasker made the change, notify admins and superadmins
            $adminRoleIds = Role::whereIn('name', ['admin', 'superadmin'])->pluck('id');
            $adminUsers = User::whereHas('roles', function ($query) use ($adminRoleIds) {
                $query->whereIn('role_id', $adminRoleIds);
            })->get();
            
            foreach ($adminUsers as $user) {
                // Don't notify the user who made the change (if they happen to be admin)
                if ($user->id !== $currentUser->id) {
                    $users[] = $user;
                }
            }
            
            Log::info('Regular user task change - notifying admins/superadmins', [
                'user' => $currentUser ? $currentUser->email : 'Unknown',
                'task_id' => $event->task->id,
                'admin_users_count' => count($users)
            ]);
        }

        // Remove duplicates
        $uniqueUsers = [];
        $seenUserIds = [];
        foreach ($users as $user) {
            if (!in_array($user->id, $seenUserIds)) {
                $uniqueUsers[] = $user;
                $seenUserIds[] = $user->id;
            }
        }

        return $uniqueUsers;
    }

    /**
     * Create notification for a specific user
     */
    private function createNotification(User $user, TaskEvent $event): void
    {
        // Don't create duplicate notifications for the same event
        $existingNotification = Notification::where([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'type' => 'TaskUpdate'
        ])
        ->where('message', 'LIKE', '%' . $event->task->title . '%')
        ->where('message', 'LIKE', '%' . $event->eventType . '%')
        ->where('created_at', '>=', now()->subMinutes(5))
        ->first();

        if ($existingNotification) {
            return;
        }

        // Prepare variables for template
        $variables = $this->prepareTaskVariables($event, $user);

        // Generate message using template
        $message = $this->notificationTemplateService->processTemplate('TaskUpdate', $variables);

        Notification::create([
            'user_id' => $user->id,
            'conference_id' => $event->conferenceId,
            'message' => $message,
            'type' => 'TaskUpdate',
            'related_model' => 'Task',
            'related_id' => $event->task->id,
            'action_url' => route('tasks.show', $event->task->id),
            'sent_at' => now(),
            'read_status' => false,
        ]);
    }

    /**
     * Prepare variables for task notification template
     */
    private function prepareTaskVariables(TaskEvent $event, User $user): array
    {
        $task = $event->task;
        $conference = $task->conference;

        return [
            'task_title' => $task->title,
            'task_description' => $task->description ?? '',
            'priority' => ucfirst($task->priority),
            'status' => ucfirst(str_replace('_', ' ', $task->status)),
            'due_date' => $task->due_date ? $task->due_date->format('M d, Y') : 'Not set',
            'action' => $this->getActionDescription($event->eventType),
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'assigned_users' => $task->users->pluck('first_name')->join(', '),
            'conference_name' => $conference->name ?? 'Unknown Conference',
        ];
    }

    /**
     * Get human-readable action description
     */
    private function getActionDescription(string $eventType): string
    {
        $actions = [
            'task_assigned' => 'assigned',
            'task_updated' => 'updated',
            'task_completed' => 'completed',
            'task_status_changed' => 'status changed',
            'task_overdue' => 'marked as overdue',
            'task_due_soon' => 'due soon',
        ];

        return $actions[$eventType] ?? 'modified';
    }
}
