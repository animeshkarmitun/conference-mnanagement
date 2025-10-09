<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Models\User;
use App\Models\Role;
use App\Services\EmailTrackingService;
use App\Services\EmailTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected EmailTrackingService $emailTrackingService;
    protected EmailTemplateService $emailTemplateService;

    /**
     * Create the event listener.
     */
    public function __construct(EmailTrackingService $emailTrackingService, EmailTemplateService $emailTemplateService)
    {
        $this->emailTrackingService = $emailTrackingService;
        $this->emailTemplateService = $emailTemplateService;
    }

    /**
     * Handle the event.
     */
    public function handle(TaskEvent $event): void
    {
        try {
            // Get users who should receive email notifications
            $usersToNotify = $this->getUsersToNotify($event);

            foreach ($usersToNotify as $user) {
                $this->sendEmailNotification($user, $event);
            }

            Log::info('Task email notification sent', [
                'event_type' => $event->eventType,
                'task_id' => $event->task->id,
                'emails_sent' => count($usersToNotify)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send task email notification', [
                'error' => $e->getMessage(),
                'event_type' => $event->eventType,
                'task_id' => $event->task->id
            ]);
        }
    }

    /**
     * Get users who should receive email notifications
     */
    private function getUsersToNotify(TaskEvent $event): array
    {
        $users = [];

        // Always notify all assigned taskers via email (many-to-many relationship)
        $assignedUsers = $event->task->users;
        foreach ($assignedUsers as $user) {
            $users[] = $user;
        }

        // Get admin and superadmin users for email notifications
        $adminRoleIds = Role::whereIn('name', ['admin', 'superadmin'])->pluck('id');
        $adminUsers = User::whereHas('roles', function ($query) use ($adminRoleIds) {
            $query->whereIn('role_id', $adminRoleIds);
        })->get();

        $users = array_merge($users, $adminUsers->toArray());

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
     * Send email notification to a specific user
     */
    private function sendEmailNotification(User $user, TaskEvent $event): void
    {
        try {
            // Prepare variables for template
            $variables = [
                'first_name' => $user->first_name ?? 'User',
                'last_name' => $user->last_name ?? '',
                'conference_name' => $event->task->conference->name ?? 'Conference',
                'task_title' => $event->task->title,
                'task_description' => $event->task->description ?? 'No description provided',
                'due_date' => $event->task->due_date ? $event->task->due_date->format('M d, Y') : 'Not specified',
                'priority' => ucfirst($event->task->priority),
                'status' => ucfirst(str_replace('_', ' ', $event->task->status)),
                'event_type' => $event->eventType,
            ];

            // Get template from service
            $template = $this->emailTemplateService->processTemplate(
                \App\Models\Email::TYPE_TASK_NOTIFICATION,
                $variables
            );

            // Build full email body
            $fullBody = $this->buildFullEmailBody($template);

            // Send tracked email
            $this->emailTrackingService->sendTrackedEmail(
                $user->email,
                $template['subject'],
                $fullBody,
                \App\Models\Email::TYPE_TASK_NOTIFICATION,
                auth()->user(), // Sender
                $event->task->conference,
                \App\Models\Task::class,
                $event->task->id,
                'task-notification',
                [
                    'task_title' => $event->task->title,
                    'event_type' => $event->eventType,
                    'conference_name' => $event->task->conference->name ?? 'Conference'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to send task email notification', [
                'user_id' => $user->id,
                'task_id' => $event->task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build full email body from template parts
     */
    private function buildFullEmailBody(array $template): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
            <div style='background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <p>{$template['greeting']}</p>
                
                <div style='margin: 20px 0;'>
                    {$template['body']}
                </div>
                
                <p style='margin: 20px 0;'>{$template['closing']}</p>
                <p style='margin: 20px 0;'>{$template['signature']}</p>
                
                <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                    This is an automated notification from the Conference Management System.
                </p>
            </div>
        </div>
        ";
    }

    /**
     * Get email subject based on event type
     */
    private function getEmailSubject(TaskEvent $event): string
    {
        $conferenceName = $event->task->conference->name ?? 'Conference';
        
        switch ($event->eventType) {
            case 'task_assigned':
                return "New Task Assigned - {$conferenceName}";
            case 'task_updated':
                return "Task Updated - {$conferenceName}";
            case 'task_completed':
                return "Task Completed - {$conferenceName}";
            case 'task_status_changed':
                return "Task Status Changed - {$conferenceName}";
            default:
                return "Task Update - {$conferenceName}";
        }
    }

    /**
     * Get email message based on event type and user
     */
    private function getEmailMessage(User $user, TaskEvent $event): string
    {
        $taskTitle = $event->task->title;
        $conferenceName = $event->task->conference->name ?? 'Conference';
        $dueDate = $event->task->due_date ? $event->task->due_date->format('M d, Y') : 'Not specified';
        $priority = ucfirst($event->task->priority);
        
        // Check if user is one of the assigned taskers or an admin
        $isTasker = $event->task->users->contains('id', $user->id);
        
        switch ($event->eventType) {
            case 'task_assigned':
                if ($isTasker) {
                    return "Dear {$user->first_name},\n\nA new task has been assigned to you for {$conferenceName}.\n\nTask: {$taskTitle}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nPlease review the task details and update the status as you progress.\n\nBest regards,\nConference Team";
                } else {
                    $assignedNames = $event->task->users->map(function($user) {
                        return $user->first_name . ' ' . $user->last_name;
                    })->join(', ');
                    return "Dear {$user->first_name},\n\nA new task has been assigned for {$conferenceName}.\n\nTask: {$taskTitle}\nAssigned To: {$assignedNames}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nBest regards,\nConference Team";
                }
                
            case 'task_updated':
                if ($isTasker) {
                    return "Dear {$user->first_name},\n\nA task assigned to you has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nPlease review the changes and update your progress accordingly.\n\nBest regards,\nConference Team";
                } else {
                    $assignedNames = $event->task->users->map(function($user) {
                        return $user->first_name . ' ' . $user->last_name;
                    })->join(', ');
                    return "Dear {$user->first_name},\n\nA task has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nAssigned To: {$assignedNames}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nBest regards,\nConference Team";
                }
                
            case 'task_completed':
                $completedByNames = $event->task->users->map(function($user) {
                    return $user->first_name . ' ' . $user->last_name;
                })->join(', ');
                return "Dear {$user->first_name},\n\nA task has been marked as completed for {$conferenceName}.\n\nTask: {$taskTitle}\nCompleted By: {$completedByNames}\n\nBest regards,\nConference Team";
                
            case 'task_status_changed':
                $status = ucfirst(str_replace('_', ' ', $event->task->status));
                if ($isTasker) {
                    return "Dear {$user->first_name},\n\nThe status of your task has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nNew Status: {$status}\n\nBest regards,\nConference Team";
                } else {
                    $assignedNames = $event->task->users->map(function($user) {
                        return $user->first_name . ' ' . $user->last_name;
                    })->join(', ');
                    return "Dear {$user->first_name},\n\nThe status of a task has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nAssigned To: {$assignedNames}\nNew Status: {$status}\n\nBest regards,\nConference Team";
                }
                
            default:
                return "Dear {$user->first_name},\n\nA task update has been made for {$conferenceName}.\n\nTask: {$taskTitle}\nEvent: {$event->message}\n\nBest regards,\nConference Team";
        }
    }
}
