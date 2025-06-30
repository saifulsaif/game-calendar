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
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'start' => $this->start?->toIso8601String(),
            'end' => $this->end?->toIso8601String(),
            'resourceId' => $this->resourceId ? (string) $this->resourceId : null,
            'backgroundColor' => $this->backgroundColor, // Use level color
            'borderColor' => $this->backgroundColor,
            'extendedProps' => [
                'field' => $this->field,
                'referee' => $this->referee,
                'notes' => $this->notes,
                'duration' => $this->duration,
                'level_id' => $this->level_id,
                'level_name' => $this->level?->name
            ]
        ];
    }
}