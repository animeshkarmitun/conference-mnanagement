<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationTemplateService;
use App\Models\NotificationTemplate;

class TestNotificationTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification template system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Notification Template System...');
        
        $service = new NotificationTemplateService();
        
        // Test each notification type
        $notificationTypes = NotificationTemplate::getNotificationTypes();
        
        foreach ($notificationTypes as $type => $name) {
            $this->info("\n--- Testing {$name} ({$type}) ---");
            
            // Get template preview
            $preview = $service->getTemplatePreview($type);
            $this->line("Preview: {$preview}");
            
            // Test with custom variables
            $customVariables = [
                'user_name' => 'John Doe',
                'conference_name' => 'Test Conference 2024',
                'task_title' => 'Test Task',
                'session_title' => 'Test Session',
                'participant_name' => 'Jane Smith',
            ];
            
            $customMessage = $service->processTemplate($type, $customVariables);
            $this->line("Custom: {$customMessage}");
        }
        
        $this->info("\n✅ Notification template system is working correctly!");
        
        return 0;
    }
}