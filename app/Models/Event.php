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

    protected $appends = ['resourceId'];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function getResourceIdAttribute()
    {
        // Access the attribute directly from attributes array to avoid circular reference
        return $this->attributes['resource_id'] ?? null;
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
            'extendedProps' => [
                'field' => $this->field,
                'referee' => $this->referee,
                'notes' => $this->notes,
                'duration' => $this->duration
            ]
        ];
    }
}