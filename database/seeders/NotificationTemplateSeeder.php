<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateVariable;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedNotificationTemplates();
        $this->seedNotificationTemplateVariables();
    }

    private function seedNotificationTemplates(): void
    {
        $templates = [
            [
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'message_template' => 'Task "{task_title}" has been {action} by {user_name}. Priority: {priority}, Due: {due_date}',
                'description' => 'Template for task-related notifications',
                'is_active' => true,
            ],
            [
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'message_template' => 'Session "{session_title}" has been {action}. {additional_info}',
                'description' => 'Template for session-related notifications',
                'is_active' => true,
            ],
            [
                'notification_type' => NotificationTemplate::TYPE_TRAVEL_UPDATE,
                'message_template' => 'Travel update for {participant_name}: {action}. {additional_info}',
                'description' => 'Template for travel-related notifications',
                'is_active' => true,
            ],
            [
                'notification_type' => NotificationTemplate::TYPE_CONFERENCE_UPDATE,
                'message_template' => 'Conference "{conference_name}" has been {action}. {additional_info}',
                'description' => 'Template for conference-wide notifications',
                'is_active' => true,
            ],
            [
                'notification_type' => NotificationTemplate::TYPE_PROFILE_UPDATE,
                'message_template' => 'Profile update for {user_name}: {action}. {additional_info}',
                'description' => 'Template for profile-related notifications',
                'is_active' => true,
            ],
            [
                'notification_type' => NotificationTemplate::TYPE_MISSING_DOCUMENTS,
                'message_template' => 'Missing documents for {participant_name}: {document_type} is {action}.',
                'description' => 'Template for missing document notifications',
                'is_active' => true,
            ],
            [
                'notification_type' => NotificationTemplate::TYPE_GENERAL,
                'message_template' => 'Notification from {system_name}: {message}',
                'description' => 'Template for general notifications',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['notification_type' => $template['notification_type']],
                $template
            );
        }
    }

    private function seedNotificationTemplateVariables(): void
    {
        $variables = [
            // System variables (available for all notification types)
            [
                'variable_name' => 'system_name',
                'notification_type' => '*',
                'variable_description' => 'Name of the system',
                'example_value' => 'Conference Management System',
                'is_system_variable' => true,
            ],
            [
                'variable_name' => 'current_date',
                'notification_type' => '*',
                'variable_description' => 'Current date',
                'example_value' => 'Jan 10, 2025',
                'is_system_variable' => true,
            ],
            [
                'variable_name' => 'current_time',
                'notification_type' => '*',
                'variable_description' => 'Current time',
                'example_value' => '2:30 PM',
                'is_system_variable' => true,
            ],
            [
                'variable_name' => 'conference_name',
                'notification_type' => '*',
                'variable_description' => 'Name of the conference',
                'example_value' => 'Digital Marketing Summit 2025',
                'is_system_variable' => true,
            ],

            // Task Update variables
            [
                'variable_name' => 'task_title',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Title of the task',
                'example_value' => 'Prepare Presentation',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'task_description',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Description of the task',
                'example_value' => 'Create slides for keynote speech',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'priority',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Priority level of the task',
                'example_value' => 'High',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'status',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Current status of the task',
                'example_value' => 'In Progress',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'due_date',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Due date of the task',
                'example_value' => 'Jan 15, 2025',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'action',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Action performed on the task',
                'example_value' => 'assigned',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'user_name',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Name of the user who performed the action',
                'example_value' => 'John Doe',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'assigned_users',
                'notification_type' => NotificationTemplate::TYPE_TASK_UPDATE,
                'variable_description' => 'Users assigned to the task',
                'example_value' => 'John Doe, Jane Smith',
                'is_system_variable' => false,
            ],

            // Session Update variables
            [
                'variable_name' => 'session_title',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'Title of the session',
                'example_value' => 'Introduction to Technology',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'session_description',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'Description of the session',
                'example_value' => 'Overview of modern technology trends',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'start_time',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'Start time of the session',
                'example_value' => '9:00 AM',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'end_time',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'End time of the session',
                'example_value' => '10:30 AM',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'venue_name',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'Name of the venue',
                'example_value' => 'Conference Center Room A',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'participant_name',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'Name of the participant',
                'example_value' => 'Jane Smith',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'additional_info',
                'notification_type' => NotificationTemplate::TYPE_SESSION_UPDATE,
                'variable_description' => 'Additional information about the session update',
                'example_value' => 'Time changed to 9:00 AM',
                'is_system_variable' => false,
            ],

            // Travel Update variables
            [
                'variable_name' => 'participant_name',
                'notification_type' => NotificationTemplate::TYPE_TRAVEL_UPDATE,
                'variable_description' => 'Name of the participant',
                'example_value' => 'Jane Smith',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'hotel_name',
                'notification_type' => NotificationTemplate::TYPE_TRAVEL_UPDATE,
                'variable_description' => 'Name of the hotel',
                'example_value' => 'Grand Hotel',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'room_number',
                'notification_type' => NotificationTemplate::TYPE_TRAVEL_UPDATE,
                'variable_description' => 'Room number',
                'example_value' => '205',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'check_in',
                'notification_type' => NotificationTemplate::TYPE_TRAVEL_UPDATE,
                'variable_description' => 'Check-in date and time',
                'example_value' => 'Jan 10, 2025 3:00 PM',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'check_out',
                'notification_type' => NotificationTemplate::TYPE_TRAVEL_UPDATE,
                'variable_description' => 'Check-out date and time',
                'example_value' => 'Jan 12, 2025 11:00 AM',
                'is_system_variable' => false,
            ],

            // Conference Update variables
            [
                'variable_name' => 'additional_info',
                'notification_type' => NotificationTemplate::TYPE_CONFERENCE_UPDATE,
                'variable_description' => 'Additional information about the conference update',
                'example_value' => 'New sessions added',
                'is_system_variable' => false,
            ],

            // Profile Update variables
            [
                'variable_name' => 'user_name',
                'notification_type' => NotificationTemplate::TYPE_PROFILE_UPDATE,
                'variable_description' => 'Name of the user',
                'example_value' => 'John Doe',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'field_updated',
                'notification_type' => NotificationTemplate::TYPE_PROFILE_UPDATE,
                'variable_description' => 'Field that was updated',
                'example_value' => 'contact information',
                'is_system_variable' => false,
            ],

            // Missing Documents variables
            [
                'variable_name' => 'participant_name',
                'notification_type' => NotificationTemplate::TYPE_MISSING_DOCUMENTS,
                'variable_description' => 'Name of the participant',
                'example_value' => 'Jane Smith',
                'is_system_variable' => false,
            ],
            [
                'variable_name' => 'document_type',
                'notification_type' => NotificationTemplate::TYPE_MISSING_DOCUMENTS,
                'variable_description' => 'Type of document that is missing',
                'example_value' => 'passport',
                'is_system_variable' => false,
            ],

            // General variables
            [
                'variable_name' => 'message',
                'notification_type' => NotificationTemplate::TYPE_GENERAL,
                'variable_description' => 'General message content',
                'example_value' => 'Please check your profile information',
                'is_system_variable' => false,
            ],
        ];

        foreach ($variables as $variable) {
            NotificationTemplateVariable::updateOrCreate(
                [
                    'variable_name' => $variable['variable_name'],
                    'notification_type' => $variable['notification_type'],
                ],
                $variable
            );
        }
    }
}