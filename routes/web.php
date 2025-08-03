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

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard AJAX endpoints
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/data', [\App\Http\Controllers\DashboardController::class, 'getDashboardData'])->name('data');
    Route::get('/conference-progress', [\App\Http\Controllers\DashboardController::class, 'getConferenceProgress'])->name('conference-progress');
    Route::get('/task-progress', [\App\Http\Controllers\DashboardController::class, 'getTaskProgress'])->name('task-progress');
    Route::get('/participant-stats', [\App\Http\Controllers\DashboardController::class, 'getParticipantStats'])->name('participant-stats');
    Route::get('/speaker-stats', [\App\Http\Controllers\DashboardController::class, 'getSpeakerStats'])->name('speaker-stats');
    Route::get('/summary-stats', [\App\Http\Controllers\DashboardController::class, 'getSummaryStats'])->name('summary-stats');
});

Route::get('/dashboard-tasker', function () {
    return view('dashboard-tasker');
})->middleware(['auth', 'verified'])->name('dashboard.tasker');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('conferences', \App\Http\Controllers\ConferenceController::class);
    Route::resource('participants', \App\Http\Controllers\ParticipantController::class);
    Route::resource('sessions', \App\Http\Controllers\SessionController::class);
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    Route::patch('/tasks/{task}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.update-status');
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
    Route::get('/admin/room-allocations', [\App\Http\Controllers\TravelController::class, 'roomAllocations'])->name('admin.room-allocations');
    Route::get('/admin/travel-manifests', [\App\Http\Controllers\TravelController::class, 'travelManifests'])->name('admin.travel-manifests');
    Route::get('/admin/travel-conflicts', [\App\Http\Controllers\TravelController::class, 'travelConflicts'])->name('admin.travel-conflicts');
    Route::get('/admin/export-manifest', [\App\Http\Controllers\TravelController::class, 'exportManifest'])->name('admin.export-manifest');
    Route::post('/admin/room-allocations/{participant}', [\App\Http\Controllers\TravelController::class, 'updateRoomAllocation'])->name('admin.room-allocations.update');
    Route::post('/admin/participants/download-biographies', [\App\Http\Controllers\ParticipantController::class, 'downloadBiographies'])->name('admin.participants.download-biographies');
    Route::post('/participants/bulk-update', [\App\Http\Controllers\ParticipantController::class, 'bulkUpdate'])->name('participants.bulk-update');
    Route::resource('venues', \App\Http\Controllers\VenueController::class);
    Route::post('/hotels', [\App\Http\Controllers\HotelController::class, 'store'])->name('hotels.store');
    
    // Event Coordinator Routes
    Route::prefix('event-coordinator')->name('event-coordinator.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\EventCoordinatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/travel-manifests', [\App\Http\Controllers\EventCoordinatorController::class, 'travelManifests'])->name('travel-manifests');
        Route::get('/export-manifest', [\App\Http\Controllers\EventCoordinatorController::class, 'exportManifest'])->name('export-manifest');
    });
});

Route::get('/speaker/register', [\App\Http\Controllers\SpeakerRegistrationController::class, 'showRegistrationForm'])->name('speaker.register');
Route::post('/speaker/register', [\App\Http\Controllers\SpeakerRegistrationController::class, 'register']);
Route::get('/speaker/registration/success', [\App\Http\Controllers\SpeakerRegistrationController::class, 'success'])->name('speaker.registration.success');

Route::get('/guide', function () {
    return view('guide');
})->name('guide');

Route::resource('users', \App\Http\Controllers\UserController::class)->middleware(['auth', 'verified']);

Route::resource('roles', \App\Http\Controllers\RoleController::class)->middleware(['auth', 'verified']);
Route::get('/roles/{role}/assign-users', [\App\Http\Controllers\RoleController::class, 'assignUsers'])->name('roles.assign-users');
Route::post('/roles/{role}/assign-users', [\App\Http\Controllers\RoleController::class, 'updateUserAssignments'])->name('roles.update-user-assignments');

Route::middleware('auth')->group(function () {
    Route::get('/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google-callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::get('/gmail', [GoogleController::class, 'showGmailThreads'])->name('gmail.index');
    Route::get('/gmail/{threadId}/reply', [GoogleController::class, 'showReplyForm'])->name('gmail.reply');
    Route::post('/gmail/{threadId}/reply', [GoogleController::class, 'sendReply'])->name('gmail.send-reply');
});

// Route::get('/dashboard', [GoogleController::class, 'showDashboard'])->name('dashboard');

Route::get('/bulk-email', [App\Http\Controllers\BulkEmailController::class, 'show'])->name('bulk.email');

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
