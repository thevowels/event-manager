<?php

use App\Http\Controllers\Api\AttendeeController;
use App\Http\Controllers\Api\EventController;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('events', EventController::class);
    // ->middleware('auth:sanctum');

Route::apiResource('events.attendees', AttendeeController::class);
    // ->middleware('auth:sanctum');
