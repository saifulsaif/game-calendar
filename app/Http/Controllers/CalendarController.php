<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Resource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function getEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        
        $events = Event::scheduled()
            ->whereBetween('start', [$start, $end])
            ->with('resource')
            ->get()
            ->map(function ($event) {
                return $event->toFullCalendarEvent();
            });

        return response()->json($events);
    }

    public function getUnscheduledEvents()
    {
        $events = Event::unscheduled()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'extendedProps' => [
                        'field' => $event->field,
                        'referee' => $event->referee,
                        'notes' => $event->notes,
                        'duration' => $event->duration
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
}