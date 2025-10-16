<?php

namespace App\Services;

use App\Models\EmailSettings;
use App\Models\EmailTemplateVariable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmailTemplateService
{
    /**
     * Cache key prefix for email templates
     */
    const CACHE_PREFIX = 'email_template_';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get email template by type
     */
    public function getTemplate(string $emailType): ?EmailSettings
    {
        $cacheKey = self::CACHE_PREFIX . $emailType;
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($emailType) {
            return EmailSettings::getByType($emailType);
        });
    }

    /**
     * Process email template with variables
     */
    public function processTemplate(string $emailType, array $variables = []): array
    {
        $template = $this->getTemplate($emailType);
        
        if (!$template) {
            Log::warning("Email template not found for type: {$emailType}");
            return $this->getDefaultTemplate($emailType);
        }

        return [
            'subject' => $this->replaceVariables($template->subject_template, $variables),
            'greeting' => $this->replaceVariables($template->greeting_template, $variables),
            'body' => $this->replaceVariables($template->body_template, $variables),
            'closing' => $this->replaceVariables($template->closing_template, $variables),
            'signature' => $this->replaceVariables($template->system_signature, $variables),
        ];
    }

    /**
     * Replace variables in template string
     */
    public function replaceVariables(string $template, array $variables = []): string
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
     * Get all available variables
     */
    public function getAvailableVariables(): array
    {
        return Cache::remember('email_template_variables', self::CACHE_TTL, function () {
            return EmailTemplateVariable::getAllVariables();
        });
    }

    /**
     * Get system variables
     */
    public function getSystemVariables(): array
    {
        return Cache::remember('email_system_variables', self::CACHE_TTL, function () {
            return EmailTemplateVariable::getSystemVariables();
        });
    }

    /**
     * Clear template cache
     */
    public function clearCache(string $emailType = null): void
    {
        if ($emailType) {
            Cache::forget(self::CACHE_PREFIX . $emailType);
        } else {
            Cache::forget('email_template_variables');
            Cache::forget('email_system_variables');
            
            // Clear all template caches
            $emailTypes = EmailSettings::getEmailTypes();
            foreach (array_keys($emailTypes) as $type) {
                Cache::forget(self::CACHE_PREFIX . $type);
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
            'admin_email' => config('mail.from.address', 'admin@example.com'),
            'support_email' => config('mail.from.address', 'support@example.com'),
            'current_year' => date('Y'),
            'current_date' => date('M d, Y'),
            'current_time' => date('g:i A'),
        ];
    }

    /**
     * Get default template for email type
     */
    private function getDefaultTemplate(string $emailType): array
    {
        $defaults = [
            'subject' => 'Notification - {conference_name}',
            'greeting' => 'Dear {first_name},',
            'body' => 'This is a notification from {system_name}.',
            'closing' => 'Best regards,',
            'signature' => 'Conference Team',
        ];

        // Type-specific defaults
        switch ($emailType) {
            case EmailSettings::TYPE_TASK_NOTIFICATION:
                $defaults['subject'] = 'Task Update - {conference_name}';
                $defaults['body'] = 'A task has been updated: {task_title}';
                break;
            case EmailSettings::TYPE_SESSION_NOTIFICATION:
                $defaults['subject'] = 'Session Update - {conference_name}';
                $defaults['body'] = 'A session has been updated: {session_title}';
                break;
            case EmailSettings::TYPE_TRAVEL_NOTIFICATION:
                $defaults['subject'] = 'Travel Update - {conference_name}';
                $defaults['body'] = 'Your travel information has been updated.';
                break;
            case EmailSettings::TYPE_CONFERENCE_UPDATE:
                $defaults['subject'] = 'Conference Update - {conference_name}';
                $defaults['body'] = 'There has been an update to the conference.';
                break;
        }

        return $defaults;
    }

    /**
     * Validate template variables
     */
    public function validateTemplate(string $template): array
    {
        $errors = [];
        $availableVariables = $this->getAvailableVariables();
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
    public function getTemplatePreview(string $emailType): array
    {
        $sampleData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'conference_name' => 'Sample Conference 2024',
            'session_title' => 'Introduction to Technology',
            'task_title' => 'Prepare Presentation',
            'due_date' => 'Dec 15, 2024',
            'priority' => 'High',
            'status' => 'In Progress',
            'venue_name' => 'Conference Center',
            'start_time' => '9:00 AM',
            'end_time' => '5:00 PM',
        ];

        return $this->processTemplate($emailType, $sampleData);
    }
}















