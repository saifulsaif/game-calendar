<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start',
        'end',
        'resource_id',
        'level_id', // Add this
        'is_scheduled',
        'field',
        'referee',
        'notes',
        'duration'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'is_scheduled' => 'boolean'
    ];

    protected $appends = ['resourceId', 'backgroundColor']; // Add backgroundColor

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function getResourceIdAttribute()
    {
        return $this->attributes['resource_id'] ?? null;
    }

    public function getBackgroundColorAttribute()
    {
        return $this->level ? $this->level->color : '#3788d8';
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true);
    }

    public function scopeUnscheduled($query)
    {
        return $query->where('is_scheduled', false);
    }

    public function toFullCalendarEvent()
    {
        // Include level name in the title
        $displayTitle = $this->title;
        if ($this->level) {
            $displayTitle = '[' . $this->level->name . '] ' . $this->title;
        }

        return [
            'id' => (string) $this->id,
            'title' => $displayTitle, // Updated title with level name
            'start' => $this->start?->toIso8601String(),
            'end' => $this->end?->toIso8601String(),
            'resourceId' => $this->resourceId ? (string) $this->resourceId : null,
            'backgroundColor' => $this->backgroundColor,
            'borderColor' => $this->backgroundColor,
            'textColor' => $this->getContrastColor($this->backgroundColor), // Add text color for readability
            'extendedProps' => [
                'field' => $this->field,
                'referee' => $this->referee,
                'notes' => $this->notes,
                'duration' => $this->duration,
                'level_id' => $this->level_id,
                'level_name' => $this->level?->name,
                'original_title' => $this->title // Store original title without level
            ]
        ];
    }

    private function getContrastColor($hexColor)
    {
        $r = hexdec(substr($hexColor, 1, 2));
        $g = hexdec(substr($hexColor, 3, 2));
        $b = hexdec(substr($hexColor, 5, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
}