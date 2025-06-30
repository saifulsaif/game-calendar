<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Resource;
use Illuminate\Http\Request;
use App\Models\Level;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        $levels = Level::active()->orderBy('order_index')->get();
        return view('calendar.index', compact('levels'));
    }

    public function getEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        
        $events = Event::scheduled()
            ->whereBetween('start', [$start, $end])
            ->with(['resource', 'level'])
            ->get()
            ->map(function ($event) {
                return $event->toFullCalendarEvent();
            });

        return response()->json($events);
    }

    public function getUnscheduledEvents()
    {
        $events = Event::unscheduled()
            ->with('level')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                'backgroundColor' => $event->backgroundColor,
                    'extendedProps' => [
                        'field' => $event->field,
                        'referee' => $event->referee,
                        'notes' => $event->notes,
                    'duration' => $event->duration,
                    'level_id' => $event->level_id,
                    'level_name' => $event->level?->name
                    ]
                ];
            });

        return response()->json($events);
    }

    public function getResources()
    {
        $resources = Resource::where('is_active', true)
            ->orderBy('order_index')
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => (string) $resource->id,
                    'title' => $resource->title,
                    'eventColor' => $resource->event_color
                ];
            });

        return response()->json($resources);
    }

    public function scheduleEvent(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'start' => 'required|date',
            'end' => 'required|date',
            'resource_id' => 'required|exists:resources,id'
        ]);

        $event = Event::findOrFail($validated['event_id']);
        
        $event->update([
            'start' => Carbon::parse($validated['start']),
            'end' => Carbon::parse($validated['end']),
            'resource_id' => $validated['resource_id'],
            'is_scheduled' => true
        ]);

        return response()->json([
            'success' => true,
            'event' => $event->toFullCalendarEvent()
        ]);
    }

    public function updateEvent(Request $request, Event $event)
    {
        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'resource_id' => 'nullable|exists:resources,id'
        ]);

        $event->update([
            'start' => Carbon::parse($validated['start']),
            'end' => Carbon::parse($validated['end']),
            'resource_id' => $validated['resource_id'] ?? $event->resource_id
        ]);

        return response()->json([
            'success' => true,
            'event' => $event->toFullCalendarEvent()
        ]);
    }

    public function unscheduleEvent(Event $event)
    {
        $event->update([
            'start' => null,
            'end' => null,
            'resource_id' => null,
            'is_scheduled' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event unscheduled successfully'
        ]);
    }

    public function createEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'referee' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'duration' => 'nullable|string'
        ]);

        $event = Event::create([
            'title' => $validated['title'],
            'field' => $validated['field'] ?? 'TBD',
            'referee' => $validated['referee'] ?? 'TBD',
            'notes' => $validated['notes'] ?? '',
            'duration' => $validated['duration'] ?? '01:30:00',
            'is_scheduled' => false
        ]);

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'extendedProps' => [
                    'field' => $event->field,
                    'referee' => $event->referee,
                    'notes' => $event->notes,
                    'duration' => $event->duration
                ]
            ]
        ]);
    }

    public function deleteEvent(Event $event)
    {
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }



    // Level Management Methods

    public function updateLevel(Request $request, Level $level)
    {
        $validated = $request->validate([
            'is_active' => 'required'
        ]);

        $status = ($validated['is_active'] == 'true') ? 1 : 0;

        $level->update(['is_active' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'Level updated successfully',
            'level' => $level
        ]);
    }

    public function updateLevelOrder(Request $request)
    {
        $validated = $request->validate([
            'levels' => 'required|array',
            'levels.*.id' => 'required|exists:levels,id',
            'levels.*.order_index' => 'required|integer|min:0'
        ]);

        foreach ($validated['levels'] as $levelData) {
            Level::where('id', $levelData['id'])
                ->update(['order_index' => $levelData['order_index']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Level order updated successfully'
        ]);
    }

    public function createLevel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $maxOrder = Level::max('order_index') ?? 0;

        $level = Level::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'order_index' => $maxOrder + 1,
            'is_active' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Level created successfully',
            'level' => $level
        ]);
    }

    public function deleteLevel(Level $level)
    {
        if ($level->events()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete level with existing events'
            ], 400);
        }

        $level->delete();

        return response()->json([
            'success' => true,
            'message' => 'Level deleted successfully'
        ]);
    }
}