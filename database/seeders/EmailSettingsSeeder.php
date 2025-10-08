<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailSettings;
use App\Models\EmailTemplateVariable;

class EmailSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create email template variables
        $this->createEmailVariables();

        // Create default email settings
        $this->createEmailSettings();
    }

    private function createEmailVariables(): void
    {
        $variables = [
            // User variables
            ['variable_name' => 'first_name', 'variable_description' => 'Recipient\'s first name', 'example_value' => 'John', 'is_system_variable' => true],
            ['variable_name' => 'last_name', 'variable_description' => 'Recipient\'s last name', 'example_value' => 'Doe', 'is_system_variable' => true],
            ['variable_name' => 'email', 'variable_description' => 'Recipient\'s email address', 'example_value' => 'john.doe@example.com', 'is_system_variable' => true],
            
            // Conference variables
            ['variable_name' => 'conference_name', 'variable_description' => 'Name of the conference', 'example_value' => 'Tech Conference 2024', 'is_system_variable' => true],
            ['variable_name' => 'conference_date', 'variable_description' => 'Conference start date', 'example_value' => 'March 15, 2024', 'is_system_variable' => true],
            ['variable_name' => 'conference_location', 'variable_description' => 'Conference location/venue', 'example_value' => 'Convention Center', 'is_system_variable' => true],
            
            // Session variables
            ['variable_name' => 'session_title', 'variable_description' => 'Title of the session', 'example_value' => 'Introduction to AI', 'is_system_variable' => true],
            ['variable_name' => 'session_date', 'variable_description' => 'Session date and time', 'example_value' => 'March 15, 2024 at 2:00 PM', 'is_system_variable' => true],
            ['variable_name' => 'session_location', 'variable_description' => 'Session location/room', 'example_value' => 'Room A-101', 'is_system_variable' => true],
            ['variable_name' => 'session_description', 'variable_description' => 'Session description', 'example_value' => 'Learn the basics of artificial intelligence', 'is_system_variable' => true],
            
            // Task variables
            ['variable_name' => 'task_title', 'variable_description' => 'Title of the task', 'example_value' => 'Prepare presentation slides', 'is_system_variable' => true],
            ['variable_name' => 'task_description', 'variable_description' => 'Task description', 'example_value' => 'Create slides for the keynote presentation', 'is_system_variable' => true],
            ['variable_name' => 'due_date', 'variable_description' => 'Task due date', 'example_value' => 'March 10, 2024', 'is_system_variable' => true],
            ['variable_name' => 'priority', 'variable_description' => 'Task priority level', 'example_value' => 'High', 'is_system_variable' => true],
            ['variable_name' => 'status', 'variable_description' => 'Current status', 'example_value' => 'In Progress', 'is_system_variable' => true],
            
            // Travel variables
            ['variable_name' => 'travel_type', 'variable_description' => 'Type of travel', 'example_value' => 'Flight', 'is_system_variable' => true],
            ['variable_name' => 'departure_date', 'variable_description' => 'Departure date', 'example_value' => 'March 14, 2024', 'is_system_variable' => true],
            ['variable_name' => 'return_date', 'variable_description' => 'Return date', 'example_value' => 'March 16, 2024', 'is_system_variable' => true],
            ['variable_name' => 'hotel_name', 'variable_description' => 'Hotel name', 'example_value' => 'Grand Hotel', 'is_system_variable' => true],
            
            // System variables
            ['variable_name' => 'system_name', 'variable_description' => 'Name of the system', 'example_value' => 'Conference Management System', 'is_system_variable' => true],
            ['variable_name' => 'admin_email', 'variable_description' => 'Admin email address', 'example_value' => 'admin@example.com', 'is_system_variable' => true],
            ['variable_name' => 'support_email', 'variable_description' => 'Support email address', 'example_value' => 'support@example.com', 'is_system_variable' => true],
            ['variable_name' => 'current_date', 'variable_description' => 'Current date', 'example_value' => 'March 1, 2024', 'is_system_variable' => true],
            ['variable_name' => 'current_time', 'variable_description' => 'Current time', 'example_value' => '2:30 PM', 'is_system_variable' => true],
            ['variable_name' => 'current_year', 'variable_description' => 'Current year', 'example_value' => '2024', 'is_system_variable' => true],
        ];

        foreach ($variables as $variable) {
            EmailTemplateVariable::updateOrCreate(
                ['variable_name' => $variable['variable_name']],
                $variable
            );
        }
    }

    private function createEmailSettings(): void
    {
        $emailSettings = [
            // Task Notification
            [
                'email_type' => EmailSettings::TYPE_TASK_NOTIFICATION,
                'subject_template' => 'Task Update - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'A task has been updated for {conference_name}:

Task: {task_title}
Priority: {priority}
Due Date: {due_date}
Status: {status}

Please review the task details and update the status as you progress.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // Session Notification
            [
                'email_type' => EmailSettings::TYPE_SESSION_NOTIFICATION,
                'subject_template' => 'Session Update - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'A session has been updated for {conference_name}:

Session: {session_title}
Date & Time: {session_date}
Location: {session_location}

Please check the session details and register if you\'re interested in participating.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // Travel Notification
            [
                'email_type' => EmailSettings::TYPE_TRAVEL_NOTIFICATION,
                'subject_template' => 'Travel Update - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'Your travel information has been updated for {conference_name}:

Travel Type: {travel_type}
Departure: {departure_date}
Return: {return_date}
Hotel: {hotel_name}

Please review your travel details and contact us if you have any questions.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // Conference Update
            [
                'email_type' => EmailSettings::TYPE_CONFERENCE_UPDATE,
                'subject_template' => 'Conference Update - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'There has been an update to {conference_name}:

We wanted to inform you about important changes to the conference. Please check the conference details for the latest information.

Conference: {conference_name}
Date: {conference_date}
Location: {conference_location}

Thank you for your participation in our conference.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // Profile Update
            [
                'email_type' => EmailSettings::TYPE_PROFILE_UPDATE,
                'subject_template' => 'Profile Update - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'Your profile has been updated for {conference_name}.

We have received and processed your profile information. Please review your profile to ensure all information is correct.

If you have any questions or need to make changes, please contact our support team.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // Passwordless Login
            [
                'email_type' => EmailSettings::TYPE_PASSWORDLESS_LOGIN,
                'subject_template' => 'Login Link - {system_name}',
                'greeting_template' => 'Hello {first_name},',
                'body_template' => 'You have requested a passwordless login to {system_name}.

Click the link below to access your account:

[Login Link]

This link will expire in 15 minutes for security reasons.

If you did not request this login, please ignore this email.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // Session Update
            [
                'email_type' => EmailSettings::TYPE_SESSION_UPDATE,
                'subject_template' => 'Session Information Updated - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'Session information has been updated for {conference_name}:

Session: {session_title}
Date & Time: {session_date}
Location: {session_location}
Description: {session_description}

Please review the updated session details.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],

            // General
            [
                'email_type' => EmailSettings::TYPE_GENERAL,
                'subject_template' => 'Notification - {conference_name}',
                'greeting_template' => 'Dear {first_name},',
                'body_template' => 'This is a notification from {system_name} regarding {conference_name}.

We wanted to keep you informed about important updates and information.

If you have any questions, please don\'t hesitate to contact our support team.',
                'closing_template' => 'Best regards,',
                'system_signature' => 'Conference Team',
            ],
        ];

        foreach ($emailSettings as $setting) {
            EmailSettings::updateOrCreate(
                ['email_type' => $setting['email_type']],
                $setting
            );
        }
    }
}