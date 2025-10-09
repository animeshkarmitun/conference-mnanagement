<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\NotificationTemplateVariable;
use App\Services\NotificationTemplateService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationTemplateController extends Controller
{
    protected NotificationTemplateService $notificationTemplateService;

    public function __construct(NotificationTemplateService $notificationTemplateService)
    {
        $this->notificationTemplateService = $notificationTemplateService;
    }

    /**
     * Display a listing of notification templates
     */
    public function index(): View
    {
        $templates = NotificationTemplate::orderBy('notification_type')->get();
        
        return view('admin.notification-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new notification template
     */
    public function create(): View
    {
        $notificationTypes = NotificationTemplate::getNotificationTypes();
        $variables = NotificationTemplateVariable::getAllVariables();
        
        return view('admin.notification-templates.create', compact('notificationTypes', 'variables'));
    }

    /**
     * Store a newly created notification template
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notification_type' => 'required|string|unique:notification_templates,notification_type',
            'message_template' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Validate template variables
        $errors = $this->notificationTemplateService->validateTemplate(
            $validated['message_template'], 
            $validated['notification_type']
        );

        if (!empty($errors)) {
            return back()->withErrors(['message_template' => implode(', ', $errors)])->withInput();
        }

        NotificationTemplate::create($validated);

        // Clear cache
        $this->notificationTemplateService->clearCache();

        return redirect()->route('admin.notification-templates.index')
            ->with('success', 'Notification template created successfully.');
    }

    /**
     * Display the specified notification template
     */
    public function show(NotificationTemplate $notificationTemplate): View
    {
        $variables = $this->notificationTemplateService->getAvailableVariables($notificationTemplate->notification_type);
        $preview = $this->notificationTemplateService->getTemplatePreview($notificationTemplate->notification_type);
        
        return view('admin.notification-templates.show', compact('notificationTemplate', 'variables', 'preview'));
    }

    /**
     * Show the form for editing the specified notification template
     */
    public function edit(NotificationTemplate $notificationTemplate): View
    {
        $notificationTypes = NotificationTemplate::getNotificationTypes();
        $variables = $this->notificationTemplateService->getAvailableVariables($notificationTemplate->notification_type);
        
        return view('admin.notification-templates.edit', compact('notificationTemplate', 'notificationTypes', 'variables'));
    }

    /**
     * Update the specified notification template
     */
    public function update(Request $request, NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'message_template' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Validate template variables
        $errors = $this->notificationTemplateService->validateTemplate(
            $validated['message_template'], 
            $notificationTemplate->notification_type
        );

        if (!empty($errors)) {
            return back()->withErrors(['message_template' => implode(', ', $errors)])->withInput();
        }

        $notificationTemplate->update($validated);

        // Clear cache
        $this->notificationTemplateService->clearCache($notificationTemplate->notification_type);

        return redirect()->route('admin.notification-templates.index')
            ->with('success', 'Notification template updated successfully.');
    }

    /**
     * Remove the specified notification template
     */
    public function destroy(NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $notificationTemplate->delete();

        // Clear cache
        $this->notificationTemplateService->clearCache($notificationTemplate->notification_type);

        return redirect()->route('admin.notification-templates.index')
            ->with('success', 'Notification template deleted successfully.');
    }

    /**
     * Toggle the active status of a notification template
     */
    public function toggle(NotificationTemplate $notificationTemplate): RedirectResponse
    {
        $notificationTemplate->update(['is_active' => !$notificationTemplate->is_active]);

        // Clear cache
        $this->notificationTemplateService->clearCache($notificationTemplate->notification_type);

        $status = $notificationTemplate->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.notification-templates.index')
            ->with('success', "Notification template {$status} successfully.");
    }

    /**
     * Preview a notification template with sample data
     */
    public function preview(NotificationTemplate $notificationTemplate): View
    {
        $preview = $this->notificationTemplateService->getTemplatePreview($notificationTemplate->notification_type);
        $variables = $this->notificationTemplateService->getAvailableVariables($notificationTemplate->notification_type);
        
        return view('admin.notification-templates.preview', compact('notificationTemplate', 'preview', 'variables'));
    }

    /**
     * Show variables management for a notification template
     */
    public function variables(NotificationTemplate $notificationTemplate): View
    {
        $variables = NotificationTemplateVariable::getByNotificationType($notificationTemplate->notification_type);
        
        return view('admin.notification-templates.variables', compact('notificationTemplate', 'variables'));
    }
}