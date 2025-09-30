<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Email;
use App\Services\EmailTrackingService;

class ManagePendingEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:pending {--list : List pending emails} {--resend : Resend pending emails} {--clear : Clear old pending emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage pending emails that require manual sending';

    protected $emailTrackingService;

    public function __construct(EmailTrackingService $emailTrackingService)
    {
        parent::__construct();
        $this->emailTrackingService = $emailTrackingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listPendingEmails();
        } elseif ($this->option('resend')) {
            $this->resendPendingEmails();
        } elseif ($this->option('clear')) {
            $this->clearOldPendingEmails();
        } else {
            $this->showHelp();
        }
    }

    private function listPendingEmails()
    {
        $pendingEmails = Email::where('status', Email::STATUS_PENDING)
            ->whereJsonContains('metadata->manual_send_required', true)
            ->with(['user', 'conference'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pendingEmails->isEmpty()) {
            $this->info('No pending emails found.');
            return;
        }

        $this->info('Pending Emails (require manual sending):');
        $this->line('');

        $headers = ['ID', 'Recipient', 'Subject', 'Type', 'Created', 'Reason'];
        $rows = [];

        foreach ($pendingEmails as $email) {
            $rows[] = [
                $email->id,
                $email->recipient_email,
                substr($email->subject, 0, 50) . '...',
                $email->email_type,
                $email->created_at->format('Y-m-d H:i'),
                $email->metadata['manual_send_reason'] ?? 'Unknown'
            ];
        }

        $this->table($headers, $rows);
        $this->line('');
        $this->info('Total pending emails: ' . $pendingEmails->count());
    }

    private function resendPendingEmails()
    {
        $pendingEmails = Email::where('status', Email::STATUS_PENDING)
            ->whereJsonContains('metadata->manual_send_required', true)
            ->get();

        if ($pendingEmails->isEmpty()) {
            $this->info('No pending emails to resend.');
            return;
        }

        $this->info('Attempting to resend ' . $pendingEmails->count() . ' pending emails...');
        $this->line('');

        $successCount = 0;
        $failCount = 0;

        foreach ($pendingEmails as $email) {
            try {
                // Try to resend the email
                $this->emailTrackingService->sendTrackedEmail(
                    $email->recipient_email,
                    $email->subject,
                    $email->body,
                    $email->email_type,
                    $email->user,
                    $email->conference,
                    $email->related_model_type,
                    $email->related_model_id,
                    $email->template_name,
                    $email->metadata
                );

                $this->line('✓ Resent email ID: ' . $email->id . ' to ' . $email->recipient_email);
                $successCount++;

            } catch (\Exception $e) {
                $this->error('✗ Failed to resend email ID: ' . $email->id . ' - ' . $e->getMessage());
                $failCount++;
            }
        }

        $this->line('');
        $this->info("Resend complete: {$successCount} successful, {$failCount} failed");
    }

    private function clearOldPendingEmails()
    {
        $oldPendingEmails = Email::where('status', Email::STATUS_PENDING)
            ->whereJsonContains('metadata->manual_send_required', true)
            ->where('created_at', '<', now()->subDays(7))
            ->get();

        if ($oldPendingEmails->isEmpty()) {
            $this->info('No old pending emails to clear.');
            return;
        }

        if ($this->confirm('Are you sure you want to clear ' . $oldPendingEmails->count() . ' old pending emails?')) {
            $deleted = $oldPendingEmails->each(function ($email) {
                $email->delete();
            });

            $this->info('Cleared ' . $oldPendingEmails->count() . ' old pending emails.');
        }
    }

    private function showHelp()
    {
        $this->info('Pending Emails Management');
        $this->line('');
        $this->line('Available options:');
        $this->line('  --list    List all pending emails that require manual sending');
        $this->line('  --resend  Attempt to resend all pending emails');
        $this->line('  --clear   Clear old pending emails (older than 7 days)');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan emails:pending --list');
        $this->line('  php artisan emails:pending --resend');
        $this->line('  php artisan emails:pending --clear');
    }
}
