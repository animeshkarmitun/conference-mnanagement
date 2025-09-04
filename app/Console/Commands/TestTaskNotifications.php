<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\User;
use App\Models\Conference;
use App\Services\TaskNotificationService;
use Illuminate\Console\Command;

class TestTaskNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:test-notifications {--type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test task notifications for different scenarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $taskNotificationService = new TaskNotificationService();

        $this->info('Testing Task Notifications...');
        $this->newLine();

        // Get a sample task
        $task = Task::with(['assignedTo', 'conference'])->first();
        
        if (!$task) {
            $this->error('No tasks found. Please create a task first.');
            return 1;
        }

        $this->info("Using task: '{$task->title}' assigned to {$task->assignedTo->first_name} {$task->assignedTo->last_name}");
        $this->newLine();

        switch ($type) {
            case 'assigned':
                $this->testTaskAssigned($task, $taskNotificationService);
                break;
            case 'updated':
                $this->testTaskUpdated($task, $taskNotificationService);
                break;
            case 'completed':
                $this->testTaskCompleted($task, $taskNotificationService);
                break;
            case 'status_changed':
                $this->testTaskStatusChanged($task, $taskNotificationService);
                break;
            case 'overdue':
                $this->testTaskOverdue($task, $taskNotificationService);
                break;
            case 'due_soon':
                $this->testTaskDueSoon($task, $taskNotificationService);
                break;
            case 'all':
            default:
                $this->testTaskAssigned($task, $taskNotificationService);
                $this->testTaskUpdated($task, $taskNotificationService);
                $this->testTaskStatusChanged($task, $taskNotificationService);
                $this->testTaskCompleted($task, $taskNotificationService);
                $this->testTaskOverdue($task, $taskNotificationService);
                $this->testTaskDueSoon($task, $taskNotificationService);
                break;
        }

        $this->info('Task notification tests completed!');
        $this->info('Check the logs and notifications table for results.');
        
        return 0;
    }

    private function testTaskAssigned(Task $task, TaskNotificationService $service)
    {
        $this->info('Testing Task Assignment Notification...');
        
        $service->notifyTaskAssigned($task);
        
        $this->info('✓ Task assignment notification sent');
        $this->newLine();
    }

    private function testTaskUpdated(Task $task, TaskNotificationService $service)
    {
        $this->info('Testing Task Update Notification...');
        
        $service->notifyTaskUpdated($task);
        
        $this->info('✓ Task update notification sent');
        $this->newLine();
    }

    private function testTaskCompleted(Task $task, TaskNotificationService $service)
    {
        $this->info('Testing Task Completion Notification...');
        
        $service->notifyTaskCompleted($task);
        
        $this->info('✓ Task completion notification sent');
        $this->newLine();
    }

    private function testTaskStatusChanged(Task $task, TaskNotificationService $service)
    {
        $this->info('Testing Task Status Change Notification...');
        
        $oldStatus = 'pending';
        $service->notifyTaskStatusChanged($task, $oldStatus);
        
        $this->info('✓ Task status change notification sent');
        $this->newLine();
    }

    private function testTaskOverdue(Task $task, TaskNotificationService $service)
    {
        $this->info('Testing Task Overdue Notification...');
        
        $service->notifyTaskOverdue($task);
        
        $this->info('✓ Task overdue notification sent');
        $this->newLine();
    }

    private function testTaskDueSoon(Task $task, TaskNotificationService $service)
    {
        $this->info('Testing Task Due Soon Notification...');
        
        $service->notifyTaskDueSoon($task);
        
        $this->info('✓ Task due soon notification sent');
        $this->newLine();
    }
}
