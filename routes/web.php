<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GoogleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Debug route for testing (no auth required)
Route::get('/debug/participants/{conferenceId}', function($conferenceId) {
    $participants = \App\Models\Participant::with(['user', 'participantType'])
        ->where('conference_id', $conferenceId)
        ->get()
        ->map(function($participant) {
            return [
                'id' => $participant->id,
                'name' => ($participant->user->first_name ?? $participant->user->name) . ' ' . ($participant->user->last_name ?? ''),
                'email' => $participant->user->email,
                'organization' => $participant->user->organization ?? '',
                'type' => $participant->participantType->name ?? '',
            ];
        });
    
    return response()->json(['participants' => $participants]);
});

// Debug route for Gmail access testing
Route::get('/debug/gmail-access', function() {
    if (!Auth::check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    $user = Auth::user();
    $roles = $user->roles->pluck('name')->toArray();
    
    return response()->json([
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $roles,
        'has_admin' => $user->hasRole('admin'),
        'has_superadmin' => $user->hasRole('superadmin'),
        'has_any_admin' => $user->hasRole('admin') || $user->hasRole('superadmin')
    ]);
});

// Debug route to test middleware behavior
Route::get('/debug/middleware-test', function() {
    if (!Auth::check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    $user = Auth::user();
    $roles = $user->roles->pluck('name')->toArray();
    
    // Simulate the middleware logic
    $hasAdmin = $user->hasRole('admin');
    $hasSuperAdmin = $user->hasRole('superadmin');
    $hasAnyAdmin = $hasAdmin || $hasSuperAdmin;
    
    return response()->json([
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $roles,
        'has_admin' => $hasAdmin,
        'has_superadmin' => $hasSuperAdmin,
        'has_any_admin' => $hasAnyAdmin,
        'would_redirect' => !$hasAnyAdmin,
        'redirect_target' => !$hasAnyAdmin ? 'participant-dashboard' : 'none'
    ]);
});

// Simple test route to check if server is working
Route::get('/test', function() {
    return response()->json(['status' => 'Server is working', 'time' => now()]);
});

// Test route to check redirect behavior
Route::get('/test-redirect', function() {
    return response()->json([
        'message' => 'No redirect loop here!',
        'time' => now(),
        'url' => request()->url()
    ]);
});

// Force cache clear route
Route::get('/clear-cache', function() {
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('cache:clear');
    
    return response()->json([
        'message' => 'All caches cleared successfully!',
        'time' => now()
    ]);
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified', 'role.redirect'])->name('dashboard');
Route::get('/participant-dashboard', [\App\Http\Controllers\ParticipantDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('participant-dashboard');

// Dashboard AJAX endpoints
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/data', [\App\Http\Controllers\DashboardController::class, 'getDashboardData'])->name('data');
    Route::get('/conference-progress', [\App\Http\Controllers\DashboardController::class, 'getConferenceProgress'])->name('conference-progress');
    Route::get('/task-progress', [\App\Http\Controllers\DashboardController::class, 'getTaskProgress'])->name('task-progress');
    Route::get('/participant-stats', [\App\Http\Controllers\DashboardController::class, 'getParticipantStats'])->name('participant-stats');
    Route::get('/speaker-stats', [\App\Http\Controllers\DashboardController::class, 'getSpeakerStats'])->name('speaker-stats');
    Route::get('/summary-stats', [\App\Http\Controllers\DashboardController::class, 'getSummaryStats'])->name('summary-stats');
    Route::get('/activities', [\App\Http\Controllers\DashboardController::class, 'activities'])->name('activities');
});

Route::get('/dashboard-tasker', [\App\Http\Controllers\TaskerDashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard.tasker');

// Debug route for tasker
Route::get('/debug-tasker', function () {
    $user = auth()->user();
    $tasks = \App\Models\Task::where('assigned_to', $user->id)->get();
    
    return response()->json([
        'user_id' => $user->id,
        'user_email' => $user->email,
        'tasks_count' => $tasks->count(),
        'tasks' => $tasks->pluck('title')->toArray()
    ]);
})->middleware(['auth', 'verified']);

// Test tasker dashboard route
Route::get('/test-tasker-dashboard', function () {
    $user = auth()->user();
    $assignedTasks = \App\Models\Task::with(['conference', 'createdBy'])
        ->where('assigned_to', $user->id)
        ->latest()
        ->get();
    
    $notifications = \App\Models\Notification::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get();
    
    return response()->json([
        'user_id' => $user->id,
        'user_email' => $user->email,
        'assigned_tasks_count' => $assignedTasks->count(),
        'assigned_tasks' => $assignedTasks->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'conference' => $task->conference->name ?? 'No Conference'
            ];
        })->toArray(),
        'notifications_count' => $notifications->count(),
        'notifications' => $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'type' => $notification->type,
                'read_status' => $notification->read_status,
                'created_at' => $notification->created_at
            ];
        })->toArray()
    ]);
})->middleware(['auth', 'verified']);

// Add route for marking notifications as read
Route::post('/notifications/{notification}/mark-read', function (\App\Models\Notification $notification) {
    // Ensure the notification belongs to the authenticated user
    if ($notification->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    $notification->update(['read_status' => true]);
    
    return response()->json([
        'success' => true,
        'notification_id' => $notification->id,
        'unread_count' => \App\Models\Notification::where('user_id', auth()->id())
            ->where('read_status', false)
            ->count()
    ]);
})->middleware(['auth', 'verified']);

// Debug route to check notifications
Route::get('/debug-notifications', function () {
    $user = auth()->user();
    $notifications = \App\Models\Notification::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get();
    
    return response()->json([
        'user_id' => $user->id,
        'notifications_count' => $notifications->count(),
        'notifications' => $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'type' => $notification->type,
                'related_model' => $notification->related_model,
                'related_id' => $notification->related_id,
                'action_url' => $notification->action_url,
                'read_status' => $notification->read_status,
                'created_at' => $notification->created_at
            ];
        })->toArray()
    ]);
})->middleware(['auth', 'verified']);

// Route to get single notification data
Route::get('/notifications/{notification}/data', function (\App\Models\Notification $notification) {
    // Ensure the notification belongs to the authenticated user
    if ($notification->user_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    return response()->json([
        'id' => $notification->id,
        'message' => $notification->message,
        'type' => $notification->type,
        'related_model' => $notification->related_model,
        'related_id' => $notification->related_id,
        'action_url' => $notification->action_url,
        'read_status' => $notification->read_status,
        'created_at' => $notification->created_at
    ]);
})->middleware(['auth', 'verified']);

// Notification routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/data', [\App\Http\Controllers\NotificationController::class, 'getNotificationData'])->name('notifications.data');
    Route::patch('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/{notification}/unread', [\App\Http\Controllers\NotificationController::class, 'markAsUnread'])->name('notifications.mark-unread');
    Route::patch('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'getRecentNotifications'])->name('notifications.recent');
});

// Admin-only notification creation routes
Route::middleware(['auth', 'verified', 'admin.access'])->group(function () {
    Route::get('/notifications/create', [\App\Http\Controllers\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\NotificationController::class, 'store'])->name('notifications.store');
});

// API endpoint to fetch users by conference
Route::get('/api/conferences/{conference}/users', function(\App\Models\Conference $conference) {
    $users = $conference->participants()->with('user')->get()->map(function($participant) {
        return [
            'id' => $participant->user->id,
            'first_name' => $participant->user->first_name,
            'last_name' => $participant->user->last_name,
            'email' => $participant->user->email,
        ];
    });
    
    return response()->json($users);
})->name('api.conferences.users');
    
// Add route for notification actions (clicking on notifications)
Route::get('/notifications/{notification}/action', function (\App\Models\Notification $notification) {
        // Ensure the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        // Mark notification as read
        $notification->update(['read_status' => true]);
        
        // Redirect to the action URL if available
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }
        
        // Fallback based on notification type
        switch ($notification->type) {
            case 'TaskUpdate':
                if ($notification->related_id) {
                    return redirect()->route('tasks.show', $notification->related_id);
                }
                break;
            case 'TravelUpdate':
                if ($notification->related_id) {
                    return redirect()->route('participants.show', $notification->related_id);
                }
                break;
            case 'SessionUpdate':
                if ($notification->related_id) {
                    return redirect()->route('sessions.show', $notification->related_id);
                }
                break;
            case 'ConferenceUpdate':
                if ($notification->related_id) {
                    return redirect()->route('conferences.show', $notification->related_id);
                }
                break;
            case 'ProfileUpdate':
                if ($notification->related_id) {
                    return redirect()->route('participants.show', $notification->related_id);
                }
                break;
            case 'General':
                // Redirect to dashboard for general notifications
                return redirect('/dashboard');
        }
        
        // Default fallback to notifications index
        return redirect()->route('notifications.index');
    })->name('notifications.action');

Route::middleware('auth')->group(function () {
    Route::get('/tasks/export', [\App\Http\Controllers\TaskController::class, 'export'])->name('tasks.export');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('conferences', \App\Http\Controllers\ConferenceController::class);
    Route::get('/conferences-export', [\App\Http\Controllers\ConferenceController::class, 'export'])->name('conferences.export');
    Route::resource('participants', \App\Http\Controllers\ParticipantController::class);
    Route::resource('sessions', \App\Http\Controllers\SessionController::class);
    Route::get('/sessions/participants/by-conference', [\App\Http\Controllers\SessionController::class, 'getParticipantsByConference'])->name('sessions.participants.by-conference');
    Route::get('/sessions/export', [\App\Http\Controllers\SessionController::class, 'export'])->name('sessions.export');
    
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    Route::patch('/tasks/{task}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('/tasks/test-export', function() { return 'Test export route works'; })->name('tasks.test-export');
    Route::resource('notifications', \App\Http\Controllers\NotificationController::class);
    Route::get('/speakers', [\App\Http\Controllers\SpeakerController::class, 'index'])->name('speakers.index');
    Route::get('/my-profile', [\App\Http\Controllers\ParticipantController::class, 'profile'])->name('participants.profile');
    Route::post('/participants/{participant}/comments', [\App\Http\Controllers\ParticipantController::class, 'storeComment'])->name('participants.comments.store');
    Route::put('/participants/{participant}/travel', [\App\Http\Controllers\ParticipantController::class, 'updateTravel'])->name('participants.travel.update');
    Route::get('/participants/{participant}/download-resume', [\App\Http\Controllers\ParticipantController::class, 'downloadResume'])->name('participants.download-resume');
    Route::get('/participants/{participant}/profile-picture', [\App\Http\Controllers\ParticipantController::class, 'showProfilePicture'])->name('participants.profile-picture');
    Route::post('/participants/{participant}/assign-session', [\App\Http\Controllers\ParticipantController::class, 'assignSession'])->name('participants.assign-session');
    Route::post('/participants/{participant}/update-status', [\App\Http\Controllers\ParticipantController::class, 'updateStatus'])->name('participants.update-status');
    Route::post('/participants/{participant}/remove-session', [\App\Http\Controllers\ParticipantController::class, 'removeSession'])->name('participants.remove-session');
    Route::post('/participants/send-email', [\App\Http\Controllers\ParticipantController::class, 'sendEmail'])->name('participants.send-email');
    
    // Admin Conference Docs Routes (must come first to avoid conflicts)
    Route::resource('conference-docs', \App\Http\Controllers\ConferenceDocController::class);
    Route::post('/conference-docs/{conferenceDoc}/media', [\App\Http\Controllers\ConferenceDocController::class, 'uploadMedia'])->name('conference-docs.media.upload');
    Route::get('/conference-docs/{conferenceDoc}/media/{docItem}/download', [\App\Http\Controllers\ConferenceDocController::class, 'downloadMedia'])->name('conference-docs.media.download');
    Route::delete('/conference-docs/{conferenceDoc}/media/{docItem}', [\App\Http\Controllers\ConferenceDocController::class, 'deleteMedia'])->name('conference-docs.media.delete');
    
    // Conference Docs Routes for Participants (more specific routes after admin routes)
    Route::get('/my-conference-docs', [\App\Http\Controllers\ConferenceDocController::class, 'participantIndex'])->name('participant.conference-docs.index');
    Route::get('/my-conference-docs/{conferenceDoc}/download', [\App\Http\Controllers\ConferenceDocController::class, 'download'])->name('participant.conference-docs.download');
    Route::get('/my-conference-docs/{conferenceDoc}/media/{docItem}/download', [\App\Http\Controllers\ConferenceDocController::class, 'downloadMedia'])->name('participant.conference-docs.media.download');
    
    // Participant Notification Routes
    Route::get('/participant/notifications', [\App\Http\Controllers\NotificationController::class, 'participantIndex'])->name('participant.notifications.index');
    
    Route::get('/admin/room-allocations', [\App\Http\Controllers\TravelController::class, 'roomAllocations'])->name('admin.room-allocations');
    Route::get('/admin/itineraries', [\App\Http\Controllers\TravelController::class, 'itineraries'])->name('admin.itineraries');
    Route::get('/admin/travel-conflicts', [\App\Http\Controllers\TravelController::class, 'travelConflicts'])->name('admin.travel-conflicts');
    Route::get('/admin/export-itinerary', [\App\Http\Controllers\TravelController::class, 'exportItinerary'])->name('admin.export-itinerary');
    Route::post('/admin/room-allocations/{participant}', [\App\Http\Controllers\TravelController::class, 'updateRoomAllocation'])->name('admin.room-allocations.update');
    Route::post('/admin/participants/download-biographies', [\App\Http\Controllers\ParticipantController::class, 'downloadBiographies'])->name('admin.participants.download-biographies');
    
    // Email Tracking Routes (Admin Only)
    Route::prefix('admin/email-tracking')->name('admin.email-tracking.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\EmailTrackingController::class, 'index'])->name('index');
        Route::get('/stats', [App\Http\Controllers\Admin\EmailTrackingController::class, 'stats'])->name('stats');
        Route::get('/emails', [App\Http\Controllers\Admin\EmailTrackingController::class, 'emails'])->name('emails');
        Route::get('/{email}', [App\Http\Controllers\Admin\EmailTrackingController::class, 'show'])->name('show');
        Route::post('/{email}/resend', [App\Http\Controllers\Admin\EmailTrackingController::class, 'resend'])->name('resend');
        Route::delete('/{email}', [App\Http\Controllers\Admin\EmailTrackingController::class, 'destroy'])->name('destroy');
        Route::get('/export/csv', [App\Http\Controllers\Admin\EmailTrackingController::class, 'export'])->name('export');
        Route::post('/cleanup', [App\Http\Controllers\Admin\EmailTrackingController::class, 'cleanup'])->name('cleanup');
        
        // Conversation routes
        Route::get('/participants', [App\Http\Controllers\Admin\EmailTrackingController::class, 'getParticipantsWithEmails'])->name('participants');
        Route::get('/conversations', [App\Http\Controllers\Admin\EmailTrackingController::class, 'getParticipantConversations'])->name('conversations');
        Route::get('/thread/{threadId}', [App\Http\Controllers\Admin\EmailTrackingController::class, 'getConversationThread'])->name('thread');
        Route::get('/search', [App\Http\Controllers\Admin\EmailTrackingController::class, 'searchParticipantEmails'])->name('search');
    });
    Route::post('/participants/bulk-update', [\App\Http\Controllers\ParticipantController::class, 'bulkUpdate'])->name('participants.bulk-update');
    Route::resource('venues', \App\Http\Controllers\VenueController::class);
    Route::post('/hotels', [\App\Http\Controllers\HotelController::class, 'store'])->name('hotels.store');
    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::post('/users/{user}/activate', [\App\Http\Controllers\UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [\App\Http\Controllers\UserController::class, 'deactivate'])->name('users.deactivate');
    
    // Participant Types Routes
    Route::resource('participant-types', \App\Http\Controllers\ParticipantTypeController::class);

    // ID Card Routes
    Route::resource('id-cards', \App\Http\Controllers\IdCardController::class);
    Route::post('/id-cards/{idCard}/toggle-status', [\App\Http\Controllers\IdCardController::class, 'toggleStatus'])->name('id-cards.toggle-status');
    Route::post('/users/{user}/generate-id-card', [\App\Http\Controllers\IdCardController::class, 'generateForUser'])->name('id-cards.generate-for-user');
    Route::post('/participants/{participant}/generate-id-card', [\App\Http\Controllers\IdCardController::class, 'generateForParticipant'])->name('id-cards.generate-for-participant');
    Route::post('/conferences/{conference}/generate-id-cards', [\App\Http\Controllers\IdCardController::class, 'generateForConference'])->name('id-cards.generate-for-conference');
    Route::get('/my-id-card', [\App\Http\Controllers\IdCardController::class, 'generateMyCard'])->name('id-cards.generate-my-card');
    
    // Event Coordinator Routes
    Route::prefix('event-coordinator')->name('event-coordinator.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\EventCoordinatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/itineraries', [\App\Http\Controllers\EventCoordinatorController::class, 'itineraries'])->name('itineraries');
        Route::get('/export-itinerary', [\App\Http\Controllers\EventCoordinatorController::class, 'exportItinerary'])->name('export-itinerary');
    });

    // Backup Management Routes (Admin Only)
    Route::prefix('admin/backup')->name('admin.backup.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\BackupController::class, 'create'])->name('create');
        Route::get('/{id}', [\App\Http\Controllers\BackupController::class, 'show'])->name('show');
        Route::delete('/{id}', [\App\Http\Controllers\BackupController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('restore');
        Route::get('/{id}/preview', [\App\Http\Controllers\BackupController::class, 'preview'])->name('preview');
        Route::get('/restore/history', [\App\Http\Controllers\BackupController::class, 'restoreHistory'])->name('restore.history');
        Route::get('/tables', [\App\Http\Controllers\BackupController::class, 'getTables'])->name('tables');
        Route::get('/stats', [\App\Http\Controllers\BackupController::class, 'stats'])->name('stats');
        Route::post('/cleanup', [\App\Http\Controllers\BackupController::class, 'cleanup'])->name('cleanup');
        Route::get('/test/connection', [\App\Http\Controllers\BackupController::class, 'testConnection'])->name('test.connection');
        Route::get('/test/simple', [\App\Http\Controllers\BackupController::class, 'testSimpleBackup'])->name('test.simple');
        Route::post('/fix-paths', [\App\Http\Controllers\BackupController::class, 'fixBackupPaths'])->name('fix.paths');
        Route::get('/test/details/{id}', [\App\Http\Controllers\BackupController::class, 'testBackupDetails'])->name('test.details');
    });
});

Route::get('/speaker/register', [\App\Http\Controllers\SpeakerRegistrationController::class, 'showRegistrationForm'])->name('speaker.register');
Route::post('/speaker/register', [\App\Http\Controllers\SpeakerRegistrationController::class, 'register']);
Route::get('/speaker/registration/success', [\App\Http\Controllers\SpeakerRegistrationController::class, 'success'])->name('speaker.registration.success');

Route::get('/guide', function () {
    return view('guide');
})->name('guide');

Route::resource('roles', \App\Http\Controllers\RoleController::class)->middleware(['auth', 'verified']);
Route::get('/roles/{role}/assign-users', [\App\Http\Controllers\RoleController::class, 'assignUsers'])->name('roles.assign-users');
Route::post('/roles/{role}/assign-users', [\App\Http\Controllers\RoleController::class, 'updateUserAssignments'])->name('roles.update-user-assignments');

Route::middleware('auth')->group(function () {
    Route::get('/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google-callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

// Gmail routes - Admin and Super Admin only
Route::middleware(['auth', 'admin.access'])->group(function () {
    Route::get('/gmail', [GoogleController::class, 'showGmailThreads'])->name('gmail.index');
    Route::get('/gmail/{threadId}/reply', [GoogleController::class, 'showReplyForm'])->name('gmail.reply');
    Route::post('/gmail/{threadId}/reply', [GoogleController::class, 'sendReply'])->name('gmail.send-reply');
    Route::get('/gmail/participants', [GoogleController::class, 'getParticipants'])->name('gmail.participants');
});

// TEMPORARY: Bypass Gmail route for testing
Route::get('/gmail-working', [GoogleController::class, 'showGmailThreads'])->middleware('auth')->name('gmail.working');

// Temporary Gmail route without middleware for testing
Route::get('/gmail-test', [GoogleController::class, 'showGmailThreads'])->name('gmail.test');

// Simple Gmail route with just auth middleware
Route::get('/gmail-simple', [GoogleController::class, 'showGmailThreads'])->middleware('auth')->name('gmail.simple');

// Completely isolated Gmail test route
Route::get('/gmail-debug', function() {
    if (!Auth::check()) {
        return response()->json(['error' => 'Not authenticated', 'redirect_to' => 'login']);
    }
    
    $user = Auth::user();
    $roles = $user->roles->pluck('name')->toArray();
    $hasAdmin = $user->hasRole('admin');
    $hasSuperAdmin = $user->hasRole('superadmin');
    
    return response()->json([
        'status' => 'success',
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $roles,
        'has_admin' => $hasAdmin,
        'has_superadmin' => $hasSuperAdmin,
        'has_any_admin' => $hasAdmin || $hasSuperAdmin,
        'message' => 'This route works without redirect loops!'
    ]);
});

// Completely bypassed Gmail route - no middleware at all
Route::get('/gmail-bypass', function() {
    return view('gmail.index', [
        'threads' => [],
        'nextPageToken' => null,
        'maxResults' => 30,
        'searchQuery' => '',
        'needsConnection' => true,
        'bypass_test' => true
    ]);
});

// Test role assignment
Route::get('/test-roles', function() {
    $user = User::where('email', 'conferencescgs@gmail.com')->first();
    if (!$user) {
        return response()->json(['error' => 'User not found']);
    }
    
    $roles = $user->roles->pluck('name')->toArray();
    $hasSuperAdmin = $user->hasRole('superadmin');
    $hasAdmin = $user->hasRole('admin');
    
    return response()->json([
        'user_id' => $user->id,
        'email' => $user->email,
        'roles' => $roles,
        'has_superadmin' => $hasSuperAdmin,
        'has_admin' => $hasAdmin,
        'role_count' => $user->roles->count()
    ]);
});

// Route::get('/dashboard', [GoogleController::class, 'showDashboard'])->name('dashboard');

Route::get('/bulk-email', [App\Http\Controllers\BulkEmailController::class, 'show'])->name('bulk.email');
Route::post('/bulk-email', [App\Http\Controllers\BulkEmailController::class, 'send'])->name('bulk.email.send');
Route::get('/bulk-email/participants', [App\Http\Controllers\BulkEmailController::class, 'getParticipants'])->name('bulk.email.participants');

// Load authentication routes if present
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}

// --- Cache clear route for environments without console access ---
Route::get('/clear-cache', [\App\Http\Controllers\CacheController::class, 'clearAll'])->name('cache.clear');

// --- DB connection check route ---
Route::get('/db-check', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection is OK!';
    } catch (\Exception $e) {
        return 'Database connection failed: ' . $e->getMessage();
    }
});



// Passwordless Login Routes
Route::prefix('passwordless-login')->name('passwordless-login.')->group(function () {
    // Public routes for participants
    Route::get('/verify/{token}', [App\Http\Controllers\PasswordlessLoginController::class, 'showVerification'])->name('verify');
    Route::post('/verify/{token}', [App\Http\Controllers\PasswordlessLoginController::class, 'verify'])->name('verify.post');
    
    // Admin routes (protected by auth middleware - role checking in controller)
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin', [App\Http\Controllers\PasswordlessLoginController::class, 'adminIndex'])->name('admin.index');
        Route::post('/generate', [App\Http\Controllers\PasswordlessLoginController::class, 'generateLink'])->name('generate');
        Route::post('/generate-bulk', [App\Http\Controllers\PasswordlessLoginController::class, 'generateBulkLinks'])->name('generate.bulk');
        Route::get('/participants', [App\Http\Controllers\PasswordlessLoginController::class, 'getParticipants'])->name('participants');
        
        Route::get('/user/{user}/links', [App\Http\Controllers\PasswordlessLoginController::class, 'getUserLinks'])->name('user.links');
        Route::delete('/user/{user}/revoke', [App\Http\Controllers\PasswordlessLoginController::class, 'revokeUserLinks'])->name('user.revoke');
        Route::post('/cleanup', [App\Http\Controllers\PasswordlessLoginController::class, 'cleanupExpired'])->name('cleanup');
        Route::get('/participants/by-type', [App\Http\Controllers\PasswordlessLoginController::class, 'getParticipantsByType'])->name('participants.by-type');
        Route::get('/participant-types', [App\Http\Controllers\PasswordlessLoginController::class, 'getParticipantTypes'])->name('participant-types');
    });
});
