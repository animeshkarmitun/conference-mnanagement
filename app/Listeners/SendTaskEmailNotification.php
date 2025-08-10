<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskEmailNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
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

        // Always notify the assigned tasker via email
        if ($event->task->assignedTo) {
            $users[] = $event->task->assignedTo;
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
        // For now, we'll log the email notification since the email system is not fully implemented
        // This can be replaced with actual email sending when the email system is ready
        
        $emailData = [
            'to' => $user->email,
            'subject' => $this->getEmailSubject($event),
            'message' => $this->getEmailMessage($user, $event),
            'task_title' => $event->task->title,
            'event_type' => $event->eventType,
            'conference_name' => $event->task->conference->name ?? 'Conference'
        ];

        // Log the email notification for now
        Log::info('Task email notification would be sent', $emailData);

        // TODO: Uncomment when email system is implemented
        // Mail::to($user->email)->send(new TaskNotificationMail($emailData));
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
        
        // Check if user is the assigned tasker or an admin
        $isTasker = $user->id === $event->task->assigned_to;
        
        switch ($event->eventType) {
            case 'task_assigned':
                if ($isTasker) {
                    return "Dear {$user->first_name},\n\nA new task has been assigned to you for {$conferenceName}.\n\nTask: {$taskTitle}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nPlease review the task details and update the status as you progress.\n\nBest regards,\nConference Team";
                } else {
                    return "Dear {$user->first_name},\n\nA new task has been assigned for {$conferenceName}.\n\nTask: {$taskTitle}\nAssigned To: {$event->task->assignedTo->first_name} {$event->task->assignedTo->last_name}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nBest regards,\nConference Team";
                }
                
            case 'task_updated':
                if ($isTasker) {
                    return "Dear {$user->first_name},\n\nA task assigned to you has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nPlease review the changes and update your progress accordingly.\n\nBest regards,\nConference Team";
                } else {
                    return "Dear {$user->first_name},\n\nA task has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nAssigned To: {$event->task->assignedTo->first_name} {$event->task->assignedTo->last_name}\nPriority: {$priority}\nDue Date: {$dueDate}\n\nBest regards,\nConference Team";
                }
                
            case 'task_completed':
                return "Dear {$user->first_name},\n\nA task has been marked as completed for {$conferenceName}.\n\nTask: {$taskTitle}\nCompleted By: {$event->task->assignedTo->first_name} {$event->task->assignedTo->last_name}\n\nBest regards,\nConference Team";
                
            case 'task_status_changed':
                $status = ucfirst(str_replace('_', ' ', $event->task->status));
                if ($isTasker) {
                    return "Dear {$user->first_name},\n\nThe status of your task has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nNew Status: {$status}\n\nBest regards,\nConference Team";
                } else {
                    return "Dear {$user->first_name},\n\nThe status of a task has been updated for {$conferenceName}.\n\nTask: {$taskTitle}\nAssigned To: {$event->task->assignedTo->first_name} {$event->task->assignedTo->last_name}\nNew Status: {$status}\n\nBest regards,\nConference Team";
                }
                
            default:
                return "Dear {$user->first_name},\n\nA task update has been made for {$conferenceName}.\n\nTask: {$taskTitle}\nEvent: {$event->message}\n\nBest regards,\nConference Team";
        }
    }
}
