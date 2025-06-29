<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    public function run()
    {
        // Create resources
        $resources = [
            ['title' => 'Group A', 'event_color' => '#3a87ad', 'order_index' => 1],
            ['title' => 'Group B', 'event_color' => '#5bb75b', 'order_index' => 2],
            ['title' => 'Group C', 'event_color' => '#faa732', 'order_index' => 3],
            ['title' => 'Group D', 'event_color' => '#da4f49', 'order_index' => 4],
        ];

        foreach ($resources as $resource) {
            Resource::create($resource);
        }

        // Create sample events
        $events = [
            // Scheduled events
            [
                'title' => 'Team A vs Team B',
                'start' => now()->setTime(10, 0),
                'end' => now()->setTime(11, 30),
                'resource_id' => 1,
                'is_scheduled' => true,
                'field' => 'Campo 1',
                'referee' => 'John Smith',
                'notes' => 'Quarter-final match'
            ],
            [
                'title' => 'Team B vs Team C',
                'start' => now()->setTime(12, 0),
                'end' => now()->setTime(13, 30),
                'resource_id' => 2,
                'is_scheduled' => true,
                'field' => 'Campo 2',
                'referee' => 'Jane Doe',
                'notes' => 'Group stage match'
            ],
            
            // Unscheduled events
            [
                'title' => 'Team D vs Team E',
                'field' => 'TBD',
                'referee' => 'TBD',
                'notes' => 'Group stage match',
                'duration' => '01:30:00'
            ],
            [
                'title' => 'Team F vs Team G',
                'field' => 'TBD',
                'referee' => 'TBD',
                'notes' => 'Quarter-final',
                'duration' => '01:30:00'
            ],
            [
                'title' => 'Team H vs Team I',
                'field' => 'TBD',
                'referee' => 'TBD',
                'notes' => 'Semi-final',
                'duration' => '01:30:00'
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}