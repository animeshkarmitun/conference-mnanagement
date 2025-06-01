<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::get('/admin/room-allocations', [\App\Http\Controllers\TravelController::class, 'roomAllocations'])->name('admin.room-allocations');
    Route::get('/admin/travel-manifests', [\App\Http\Controllers\TravelController::class, 'travelManifests'])->name('admin.travel-manifests');
    Route::get('/admin/travel-conflicts', [\App\Http\Controllers\TravelController::class, 'travelConflicts'])->name('admin.travel-conflicts');
    Route::post('/admin/room-allocations/{participant}', [\App\Http\Controllers\TravelController::class, 'updateRoomAllocation'])->name('admin.room-allocations.update');
});

Route::get('/speaker/register', [\App\Http\Controllers\SpeakerRegistrationController::class, 'showRegistrationForm'])->name('speaker.register');
Route::post('/speaker/register', [\App\Http\Controllers\SpeakerRegistrationController::class, 'register']);
Route::get('/speaker/registration/success', [\App\Http\Controllers\SpeakerRegistrationController::class, 'success'])->name('speaker.registration.success');

Route::get('/guide', function () {
    return view('guide');
})->name('guide');

// Load authentication routes if present
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
