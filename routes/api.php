<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Hotel and Room API routes
Route::get('/hotels/{hotel}/rooms', [App\Http\Controllers\HotelController::class, 'getRooms']);
Route::get('/hotels/{hotel}/times', [App\Http\Controllers\HotelController::class, 'getHotelTimes']);
Route::post('/rooms/check-conflicts', [App\Http\Controllers\RoomController::class, 'checkConflicts']);
