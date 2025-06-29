<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('calendar_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('type')->default('string'); // string, integer, boolean, time
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('calendar_settings')->insert([
            [
                'key' => 'slot_duration',
                'value' => '00:05:00',
                'type' => 'time',
                'description' => 'Time slot duration in minutes',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'slot_label_interval',
                'value' => '00:15:00',
                'type' => 'time',
                'description' => 'Interval for displaying time labels',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'snap_duration',
                'value' => '00:05:00',
                'type' => 'time',
                'description' => 'Snap interval when dragging events',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'slot_min_time',
                'value' => '00:00:00',
                'type' => 'time',
                'description' => 'Calendar start time',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'slot_max_time',
                'value' => '24:00:00',
                'type' => 'time',
                'description' => 'Calendar end time',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'time_format_12h',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Use 12-hour format (AM/PM)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('calendar_settings');
    }
};
