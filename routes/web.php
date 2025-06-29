<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;


// Calendar views
Route::get('/', [CalendarController::class, 'index'])->name('calendar.index');

// API routes for calendar
Route::prefix('api/calendar')->group(function () {
    Route::get('/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::get('/unscheduled-events', [CalendarController::class, 'getUnscheduledEvents'])->name('calendar.unscheduled');
    Route::get('/resources', [CalendarController::class, 'getResources'])->name('calendar.resources');
    
    Route::post('/events', [CalendarController::class, 'createEvent'])->name('calendar.create');
    Route::post('/events/{event}/schedule', [CalendarController::class, 'scheduleEvent'])->name('calendar.schedule');
    Route::put('/events/{event}', [CalendarController::class, 'updateEvent'])->name('calendar.update');
    Route::post('/events/{event}/unschedule', [CalendarController::class, 'unscheduleEvent'])->name('calendar.unschedule');
    Route::delete('/events/{event}', [CalendarController::class, 'deleteEvent'])->name('calendar.delete');
});