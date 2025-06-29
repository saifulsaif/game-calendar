<?php

namespace App\Http\Controllers;

use App\Models\CalendarSetting;
use App\Models\Resource;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    
    public function getSettings()
    {
        $settings = CalendarSetting::all()->keyBy('key')->map(function ($setting) {
            return [
                'value' => $setting->typed_value,
                'type' => $setting->type,
                'description' => $setting->description
            ];
        });

        // Format for FullCalendar
        $calendarConfig = [
            'slotDuration' => $settings['slot_duration']['value'] ?? '00:05:00',
            'slotLabelInterval' => $settings['slot_label_interval']['value'] ?? '00:15:00',
            'snapDuration' => $settings['snap_duration']['value'] ?? '00:05:00',
            'slotMinTime' => $settings['slot_min_time']['value'] ?? '00:00:00',
            'slotMaxTime' => $settings['slot_max_time']['value'] ?? '24:00:00',
            'slotLabelFormat' => [
                'hour' => 'numeric',
                'minute' => '2-digit',
                'omitZeroMinute' => false,
                'meridiem' => ($settings['time_format_12h']['value'] ?? true) ? 'short' : false
            ]
        ];

        return response()->json($calendarConfig);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'slot_duration' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'slot_label_interval' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'snap_duration' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'slot_min_time' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            'slot_max_time' => 'nullable|regex:/^\d{2}:\d{2}:\d{2}$/',
            // 'time_format_12h' => 'nullable|boolean'
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $type = $key === 'time_format_12h' ? 'boolean' : 'time';
                CalendarSetting::setSetting($key, $value, $type);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    public function settingsPage()
    {
        $settings = CalendarSetting::all()->keyBy('key');
        $resources = Resource::orderBy('order_index')->get();
        return view('calendar.settings', compact('settings', 'resources'));
    }

    public function updateResource(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'is_active' => 'nullable'
        ]);

        $status = ($validated['is_active'] == 'true') ? 1 : 0;

        $resource->update(['is_active' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'Resource updated successfully',
            'resource' => $resource
        ]);
    }

    public function updateResourceOrder(Request $request)
    {
        $validated = $request->validate([
            'resources' => 'required|array',
            'resources.*.id' => 'required|exists:resources,id',
            'resources.*.order_index' => 'required|integer|min:0'
        ]);

        foreach ($validated['resources'] as $resourceData) {
            Resource::where('id', $resourceData['id'])
                ->update(['order_index' => $resourceData['order_index']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resource order updated successfully'
        ]);
    }

    public function createResource(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $maxOrder = Resource::max('order_index') ?? 0;

        $resource = Resource::create([
            'title' => $validated['title'],
            'event_color' => $validated['event_color'],
            'order_index' => $maxOrder + 1,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource created successfully',
            'resource' => $resource
        ]);
    }

    public function deleteResource(Resource $resource)
    {
        // Check if resource has events
        if ($resource->events()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete resource with existing events'
            ], 400);
        }

        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource deleted successfully'
        ]);
    }

}
