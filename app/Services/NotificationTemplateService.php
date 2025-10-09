<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateVariable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationTemplateService
{
    /**
     * Cache key prefix for notification templates
     */
    const CACHE_PREFIX = 'notification_template_';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get notification template by type
     */
    public function getTemplate(string $notificationType): ?NotificationTemplate
    {
        $cacheKey = self::CACHE_PREFIX . $notificationType;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($notificationType) {
            return NotificationTemplate::getByType($notificationType);
        });
    }

    /**
     * Process notification template with variables
     */
    public function processTemplate(string $notificationType, array $variables = []): string
    {
        $template = $this->getTemplate($notificationType);
        
        if (!$template) {
            Log::warning("Notification template not found for type: {$notificationType}");
            return $this->getDefaultMessage($notificationType, $variables);
        }

        return $this->replaceVariables($template->message_template, $variables, $notificationType);
    }

    /**
     * Replace variables in template string
     */
    public function replaceVariables(string $template, array $variables = [], string $notificationType = null): string
    {
        // Add default system variables
        $defaultVariables = $this->getDefaultVariables();
        $allVariables = array_merge($defaultVariables, $variables);

        $result = $template;
        
        foreach ($allVariables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $result = str_replace($placeholder, $value ?? '', $result);
        }

        return $result;
    }

    /**
     * Get all available variables for a notification type
     */
    public function getAvailableVariables(string $notificationType): array
    {
        $cacheKey = 'notification_template_variables_' . $notificationType;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($notificationType) {
            return NotificationTemplateVariable::getByNotificationType($notificationType);
        });
    }

    /**
     * Get system variables
     */
    public function getSystemVariables(): array
    {
        return Cache::remember('notification_system_variables', self::CACHE_TTL, function () {
            return NotificationTemplateVariable::getSystemVariables();
        });
    }

    /**
     * Clear template cache
     */
    public function clearCache(string $notificationType = null): void
    {
        if ($notificationType) {
            Cache::forget(self::CACHE_PREFIX . $notificationType);
            Cache::forget('notification_template_variables_' . $notificationType);
        } else {
            Cache::forget('notification_template_variables');
            Cache::forget('notification_system_variables');
            
            // Clear all template caches
            $notificationTypes = NotificationTemplate::getNotificationTypes();
            foreach (array_keys($notificationTypes) as $type) {
                Cache::forget(self::CACHE_PREFIX . $type);
                Cache::forget('notification_template_variables_' . $type);
            }
        }
    }

    /**
     * Get default variables
     */
    private function getDefaultVariables(): array
    {
        return [
            'system_name' => config('app.name', 'Conference Management System'),
            'current_year' => date('Y'),
            'current_date' => date('M d, Y'),
            'current_time' => date('g:i A'),
        ];
    }

    /**
     * Get default message for notification type
     */
    private function getDefaultMessage(string $notificationType, array $variables = []): string
    {
        $defaults = [
            NotificationTemplate::TYPE_TASK_UPDATE => 'Task update: {task_title}',
            NotificationTemplate::TYPE_SESSION_UPDATE => 'Session update: {session_title}',
            NotificationTemplate::TYPE_TRAVEL_UPDATE => 'Travel update for {participant_name}',
            NotificationTemplate::TYPE_CONFERENCE_UPDATE => 'Conference update: {conference_name}',
            NotificationTemplate::TYPE_PROFILE_UPDATE => 'Profile update for {user_name}',
            NotificationTemplate::TYPE_MISSING_DOCUMENTS => 'Missing documents for {participant_name}',
            NotificationTemplate::TYPE_GENERAL => 'Notification from {system_name}',
        ];

        $message = $defaults[$notificationType] ?? 'Notification from {system_name}';
        return $this->replaceVariables($message, $variables);
    }

    /**
     * Validate template variables
     */
    public function validateTemplate(string $template, string $notificationType): array
    {
        $errors = [];
        $availableVariables = $this->getAvailableVariables($notificationType);
        $variableNames = array_column($availableVariables, 'variable_name');

        // Find all variables in template
        preg_match_all('/\{([^}]+)\}/', $template, $matches);
        $usedVariables = $matches[1] ?? [];

        foreach ($usedVariables as $variable) {
            if (!in_array($variable, $variableNames)) {
                $errors[] = "Unknown variable: {$variable}";
            }
        }

        return $errors;
    }

    /**
     * Get template preview with sample data
     */
    public function getTemplatePreview(string $notificationType): string
    {
        $sampleData = $this->getSampleData($notificationType);
        return $this->processTemplate($notificationType, $sampleData);
    }

    /**
     * Get sample data for preview
     */
    private function getSampleData(string $notificationType): array
    {
        $sampleData = [
            'system_name' => 'Conference Management System',
            'conference_name' => 'Sample Conference 2024',
            'user_name' => 'John Doe',
            'participant_name' => 'Jane Smith',
            'current_date' => date('M d, Y'),
            'current_time' => date('g:i A'),
        ];

        // Type-specific sample data
        switch ($notificationType) {
            case NotificationTemplate::TYPE_TASK_UPDATE:
                $sampleData = array_merge($sampleData, [
                    'task_title' => 'Prepare Presentation',
                    'task_description' => 'Create slides for the keynote speech',
                    'priority' => 'High',
                    'status' => 'In Progress',
                    'due_date' => 'Dec 15, 2024',
                    'action' => 'updated',
                    'assigned_users' => 'John Doe, Jane Smith',
                ]);
                break;
            case NotificationTemplate::TYPE_SESSION_UPDATE:
                $sampleData = array_merge($sampleData, [
                    'session_title' => 'Introduction to Technology',
                    'session_description' => 'Overview of modern technology trends',
                    'start_time' => '9:00 AM',
                    'end_time' => '10:30 AM',
                    'venue_name' => 'Conference Center Room A',
                    'action' => 'updated',
                    'additional_info' => 'Time changed to 9:00 AM',
                ]);
                break;
            case NotificationTemplate::TYPE_TRAVEL_UPDATE:
                $sampleData = array_merge($sampleData, [
                    'hotel_name' => 'Grand Hotel',
                    'room_number' => '205',
                    'check_in' => 'Dec 10, 2024 3:00 PM',
                    'check_out' => 'Dec 12, 2024 11:00 AM',
                    'action' => 'room allocated',
                ]);
                break;
            case NotificationTemplate::TYPE_CONFERENCE_UPDATE:
                $sampleData = array_merge($sampleData, [
                    'action' => 'schedule updated',
                    'additional_info' => 'New sessions added',
                ]);
                break;
            case NotificationTemplate::TYPE_PROFILE_UPDATE:
                $sampleData = array_merge($sampleData, [
                    'action' => 'profile information updated',
                    'field_updated' => 'contact information',
                ]);
                break;
            case NotificationTemplate::TYPE_MISSING_DOCUMENTS:
                $sampleData = array_merge($sampleData, [
                    'document_type' => 'passport',
                    'action' => 'required',
                ]);
                break;
        }

        return $sampleData;
    }
}


