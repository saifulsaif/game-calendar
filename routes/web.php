<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingController;


// Calendar views
Route::get('/', [CalendarController::class, 'index'])->name('calendar.index');

// API routes for calendar
Route::prefix('api/calendar')->group(function () {
    Route::get('/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
    Route::get('/unscheduled-events', [CalendarController::class, 'getUnscheduledEvents'])->name('calendar.unscheduled');
    Route::get('/resources', [CalendarController::class, 'getResources'])->name('calendar.resources');
    
    Route::post('/events', [CalendarController::class, 'createEvent'])->name('calendar.create');
    Route::post('/events/{event}/schedule', [CalendarController::class, 'scheduleEvent'])->name('calendar.schedule');
    Route::post('/events/{event}', [CalendarController::class, 'updateEvent'])->name('calendar.update');
    Route::post('/events/{event}/unschedule', [CalendarController::class, 'unscheduleEvent'])->name('calendar.unschedule');
    Route::delete('/events/{event}', [CalendarController::class, 'deleteEvent'])->name('calendar.delete');

    Route::get('/settings', [SettingController::class, 'getSettings'])->name('calendar.settings.get');
    Route::put('/settings', [SettingController::class, 'updateSettings'])->name('calendar.settings.update');
    Route::put('/resources/{resource}', [SettingController::class, 'updateResource'])->name('calendar.resources.update');
    Route::post('/resources', [SettingController::class, 'createResource'])->name('calendar.resources.create');
    Route::delete('/resources/{resource}', [SettingController::class, 'deleteResource'])->name('calendar.resources.delete');
    Route::put('/resources-order', [SettingController::class, 'updateResourceOrder'])->name('calendar.resources.order');

    Route::get('/levels', [CalendarController::class, 'getLevels'])->name('calendar.levels');
    Route::put('/levels/{level}', [CalendarController::class, 'updateLevel'])->name('calendar.levels.update');
    Route::post('/levels', [CalendarController::class, 'createLevel'])->name('calendar.levels.create');
    Route::delete('/levels/{level}', [CalendarController::class, 'deleteLevel'])->name('calendar.levels.delete');
    Route::put('/levels-order', [CalendarController::class, 'updateLevelOrder'])->name('calendar.levels.order');
});

Route::get('/calendar/settings', [SettingController::class, 'settingsPage'])->name('calendar.settings');