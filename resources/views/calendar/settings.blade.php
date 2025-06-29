{{-- resources/views/calendar/settings.blade.php --}}
@extends('layouts.app')

@section('title', 'Calendar Settings')

@push('styles')
<style>
    .settings-form {
        max-width: 600px;
        margin: 0 auto;
    }
    .time-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .time-preset {
        cursor: pointer;
        padding: 5px 10px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    .time-preset:hover {
        background: #e9ecef;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Calendar Time Settings</h2>
                <a href="{{ route('calendar.index') }}" class="btn btn-secondary">Back to Calendar</a>
            </div>

            <div class="settings-form">
                <form id="settingsForm">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label">Slot Duration</label>
                                <div class="time-input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="slot_duration" 
                                           name="slot_duration" 
                                           value="{{ $settings['slot_duration']->value ?? '00:05:00' }}"
                                           pattern="\d{2}:\d{2}:\d{2}"
                                           placeholder="HH:MM:SS">
                                    <span class="time-preset" data-target="slot_duration" data-value="00:05:00">5 min</span>
                                    <span class="time-preset" data-target="slot_duration" data-value="00:10:00">10 min</span>
                                    <span class="time-preset" data-target="slot_duration" data-value="00:15:00">15 min</span>
                                    <span class="time-preset" data-target="slot_duration" data-value="00:30:00">30 min</span>
                                </div>
                                <small class="text-muted">{{ $settings['slot_duration']->description ?? 'Time slot duration' }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Label Display Interval</label>
                                <div class="time-input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="slot_label_interval" 
                                           name="slot_label_interval" 
                                           value="{{ $settings['slot_label_interval']->value ?? '00:15:00' }}"
                                           pattern="\d{2}:\d{2}:\d{2}"
                                           placeholder="HH:MM:SS">
                                    <span class="time-preset" data-target="slot_label_interval" data-value="00:15:00">15 min</span>
                                    <span class="time-preset" data-target="slot_label_interval" data-value="00:30:00">30 min</span>
                                    <span class="time-preset" data-target="slot_label_interval" data-value="01:00:00">1 hour</span>
                                </div>
                                <small class="text-muted">{{ $settings['slot_label_interval']->description ?? 'How often to show time labels' }}</small>
                            </div>

                            {{-- <div class="mb-4">
                                <label class="form-label">Snap Duration (Drag Precision)</label>
                                <div class="time-input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="snap_duration" 
                                           name="snap_duration" 
                                           value="{{ $settings['snap_duration']->value ?? '00:05:00' }}"
                                           pattern="\d{2}:\d{2}:\d{2}"
                                           placeholder="HH:MM:SS">
                                    <span class="time-preset" data-target="snap_duration" data-value="00:05:00">5 min</span>
                                    <span class="time-preset" data-target="snap_duration" data-value="00:10:00">10 min</span>
                                    <span class="time-preset" data-target="snap_duration" data-value="00:15:00">15 min</span>
                                </div>
                                <small class="text-muted">{{ $settings['snap_duration']->description ?? 'Snap interval when dragging' }}</small>
                            </div> --}}

                            <div class="mb-4">
                                <label class="form-label">Calendar Start Time</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="slot_min_time" 
                                       name="slot_min_time" 
                                       value="{{ $settings['slot_min_time']->value ?? '00:00:00' }}"
                                       pattern="\d{2}:\d{2}:\d{2}"
                                       placeholder="HH:MM:SS">
                                <small class="text-muted">{{ $settings['slot_min_time']->description ?? 'Earliest time shown' }}</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Calendar End Time</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="slot_max_time" 
                                       name="slot_max_time" 
                                       value="{{ $settings['slot_max_time']->value ?? '24:00:00' }}"
                                       pattern="\d{2}:\d{2}:\d{2}"
                                       placeholder="HH:MM:SS">
                                <small class="text-muted">{{ $settings['slot_max_time']->description ?? 'Latest time shown' }}</small>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="time_format_12h" 
                                           name="time_format_12h" 
                                           value="1"
                                           {{ ($settings['time_format_12h']->value ?? 'true') === 'true' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="time_format_12h">
                                        Use 12-hour format (AM/PM)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                                <button type="button" class="btn btn-secondary" id="resetDefaults">Reset to Defaults</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle preset clicks
    $('.time-preset').click(function() {
        const target = $(this).data('target');
        const value = $(this).data('value');
        $('#' + target).val(value);
    });

    // Handle form submission
    $('#settingsForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            slot_duration: $('#slot_duration').val(),
            slot_label_interval: $('#slot_label_interval').val(),
            snap_duration: $('#snap_duration').val(),
            slot_min_time: $('#slot_min_time').val(),
            slot_max_time: $('#slot_max_time').val(),
            time_format_12h: $('#time_format_12h').is(':checked')
        };

        $.ajax({
            url: '{{ route("calendar.settings.update") }}',
            method: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Settings saved successfully!');
            },
            error: function() {
                alert('Failed to save settings');
            }
        });
    });

    // Reset to defaults
    $('#resetDefaults').click(function() {
        if (confirm('Reset all settings to defaults?')) {
            $('#slot_duration').val('00:05:00');
            $('#slot_label_interval').val('00:15:00');
            $('#snap_duration').val('00:05:00');
            $('#slot_min_time').val('00:00:00');
            $('#slot_max_time').val('24:00:00');
            $('#time_format_12h').prop('checked', true);
        }
    });
});
</script>
@endpush