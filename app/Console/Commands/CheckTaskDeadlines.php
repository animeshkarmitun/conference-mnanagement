<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\TaskNotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckTaskDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue tasks and tasks due soon, send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $taskNotificationService = new TaskNotificationService();
        $now = Carbon::now();

        $this->info('Checking task deadlines...');
        $this->newLine();

        // Check for overdue tasks
        $overdueTasks = Task::where('due_date', '<', $now)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->with(['assignedTo', 'conference'])
            ->get();

        $this->info("Found {$overdueTasks->count()} overdue tasks");

        foreach ($overdueTasks as $task) {
            $this->info("Sending overdue notification for task: '{$task->title}'");
            $taskNotificationService->notifyTaskOverdue($task);
        }

        // Check for tasks due within 24 hours
        $dueSoonTasks = Task::where('due_date', '>=', $now)
            ->where('due_date', '<=', $now->copy()->addDay())
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->with(['assignedTo', 'conference'])
            ->get();

        $this->info("Found {$dueSoonTasks->count()} tasks due soon");

        foreach ($dueSoonTasks as $task) {
            $this->info("Sending due soon notification for task: '{$task->title}'");
            $taskNotificationService->notifyTaskDueSoon($task);
        }

        $this->info('Task deadline check completed!');
        
        return 0;
    }
}
