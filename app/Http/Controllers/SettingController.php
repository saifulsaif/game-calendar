<?php

namespace App\Http\Controllers;

use App\Models\CalendarSetting;
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
        return view('calendar.settings', compact('settings'));
    }

}
