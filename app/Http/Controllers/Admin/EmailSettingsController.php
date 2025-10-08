<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSettings;
use App\Models\EmailTemplateVariable;
use App\Services\EmailTemplateService;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmailSettingsController extends Controller
{
    protected EmailTemplateService $emailTemplateService;
    protected EmailTrackingService $emailTrackingService;

    public function __construct(EmailTemplateService $emailTemplateService, EmailTrackingService $emailTrackingService)
    {
        $this->emailTemplateService = $emailTemplateService;
        $this->emailTrackingService = $emailTrackingService;
    }

    /**
     * Display a listing of email settings
     */
    public function index(): View
    {
        $emailSettings = EmailSettings::getAllActive();
        $emailTypes = EmailSettings::getEmailTypes();
        
        return view('admin.email-settings.index', compact('emailSettings', 'emailTypes'));
    }

    /**
     * Show the form for editing the specified email setting
     */
    public function edit(string $type): View
    {
        $emailSetting = EmailSettings::getByType($type);
        
        if (!$emailSetting) {
            abort(404, 'Email setting not found');
        }

        $availableVariables = $this->emailTemplateService->getAvailableVariables();
        $emailTypes = EmailSettings::getEmailTypes();
        
        return view('admin.email-settings.edit', compact('emailSetting', 'availableVariables', 'emailTypes'));
    }

    /**
     * Update the specified email setting
     */
    public function update(Request $request, string $type): RedirectResponse
    {
        $emailSetting = EmailSettings::getByType($type);
        
        if (!$emailSetting) {
            abort(404, 'Email setting not found');
        }

        $validator = Validator::make($request->all(), [
            'subject_template' => 'required|string|max:1000',
            'greeting_template' => 'required|string|max:1000',
            'body_template' => 'required|string|max:10000',
            'closing_template' => 'required|string|max:1000',
            'system_signature' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate template variables
        $templates = [
            'subject_template',
            'greeting_template', 
            'body_template',
            'closing_template',
            'system_signature'
        ];

        $validationErrors = [];
        foreach ($templates as $template) {
            $errors = $this->emailTemplateService->validateTemplate($request->input($template));
            if (!empty($errors)) {
                $validationErrors[$template] = $errors;
            }
        }

        if (!empty($validationErrors)) {
            return redirect()->back()
                ->withErrors($validationErrors)
                ->withInput();
        }

        try {
            $emailSetting->update($request->only([
                'subject_template',
                'greeting_template',
                'body_template',
                'closing_template',
                'system_signature'
            ]));

            // Clear cache
            $this->emailTemplateService->clearCache($type);

            return redirect()->route('admin.email-settings.index')
                ->with('success', 'Email settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update email settings', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update email settings. Please try again.')
                ->withInput();
        }
    }

    /**
     * Preview email template
     */
    public function preview(string $type): View
    {
        $emailSetting = EmailSettings::getByType($type);
        
        if (!$emailSetting) {
            abort(404, 'Email setting not found');
        }

        $preview = $this->emailTemplateService->getTemplatePreview($type);
        $emailTypes = EmailSettings::getEmailTypes();
        
        return view('admin.email-settings.preview', compact('emailSetting', 'preview', 'emailTypes'));
    }

    /**
     * Send test email
     */
    public function test(Request $request, string $type): RedirectResponse
    {
        $request->validate([
            'test_email' => 'required|email'
        ]);

        $emailSetting = EmailSettings::getByType($type);
        
        if (!$emailSetting) {
            abort(404, 'Email setting not found');
        }

        try {
            $preview = $this->emailTemplateService->getTemplatePreview($type);
            
            $subject = $preview['subject'];
            $body = $this->buildFullEmailBody($preview);

            $this->emailTrackingService->sendTrackedEmailViaGmail(
                $request->test_email,
                $subject,
                $body,
                $type,
                auth()->user(),
                null, // conference
                'email_settings',
                null, // related model id
                'test-email',
                ['test' => true]
            );

            return redirect()->back()
                ->with('success', 'Test email sent successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to send test email', [
                'type' => $type,
                'email' => $request->test_email,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send test email. Please try again.');
        }
    }

    /**
     * Reset email setting to default
     */
    public function reset(string $type): RedirectResponse
    {
        $emailSetting = EmailSettings::getByType($type);
        
        if (!$emailSetting) {
            abort(404, 'Email setting not found');
        }

        try {
            $defaults = $this->getDefaultTemplate($type);
            
            $emailSetting->update($defaults);
            
            // Clear cache
            $this->emailTemplateService->clearCache($type);

            return redirect()->route('admin.email-settings.index')
                ->with('success', 'Email settings reset to default successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to reset email settings', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to reset email settings. Please try again.');
        }
    }

    /**
     * Build full email body from template parts
     */
    private function buildFullEmailBody(array $preview): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;'>
            <div style='background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <p>{$preview['greeting']}</p>
                
                <div style='margin: 20px 0;'>
                    {$preview['body']}
                </div>
                
                <p style='margin: 20px 0;'>{$preview['closing']}</p>
                <p style='margin: 20px 0;'>{$preview['signature']}</p>
                
                <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                <p style='color: #9ca3af; font-size: 12px; text-align: center;'>
                    This is a test email from the Conference Management System.
                </p>
            </div>
        </div>
        ";
    }

    /**
     * Get default template for email type
     */
    private function getDefaultTemplate(string $type): array
    {
        $defaults = [
            'subject_template' => 'Notification - {conference_name}',
            'greeting_template' => 'Dear {first_name},',
            'body_template' => 'This is a notification from {system_name}.',
            'closing_template' => 'Best regards,',
            'system_signature' => 'Conference Team',
        ];

        // Type-specific defaults
        switch ($type) {
            case EmailSettings::TYPE_TASK_NOTIFICATION:
                $defaults = [
                    'subject_template' => 'Task Update - {conference_name}',
                    'greeting_template' => 'Dear {first_name},',
                    'body_template' => 'A task has been updated: {task_title}',
                    'closing_template' => 'Best regards,',
                    'system_signature' => 'Conference Team',
                ];
                break;
            case EmailSettings::TYPE_SESSION_NOTIFICATION:
                $defaults = [
                    'subject_template' => 'Session Update - {conference_name}',
                    'greeting_template' => 'Dear {first_name},',
                    'body_template' => 'A session has been updated: {session_title}',
                    'closing_template' => 'Best regards,',
                    'system_signature' => 'Conference Team',
                ];
                break;
            case EmailSettings::TYPE_TRAVEL_NOTIFICATION:
                $defaults = [
                    'subject_template' => 'Travel Update - {conference_name}',
                    'greeting_template' => 'Dear {first_name},',
                    'body_template' => 'Your travel information has been updated.',
                    'closing_template' => 'Best regards,',
                    'system_signature' => 'Conference Team',
                ];
                break;
            case EmailSettings::TYPE_CONFERENCE_UPDATE:
                $defaults = [
                    'subject_template' => 'Conference Update - {conference_name}',
                    'greeting_template' => 'Dear {first_name},',
                    'body_template' => 'There has been an update to the conference.',
                    'closing_template' => 'Best regards,',
                    'system_signature' => 'Conference Team',
                ];
                break;
        }

        return $defaults;
    }
}