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
    .drag-handle {
        color: #6c757d;
    }
    
    .table-secondary {
        opacity: 0.7;
    }
    
    #resourcesTable tbody tr {
        transition: background-color 0.2s;
    }
    
    #resourcesTable tbody tr.ui-sortable-helper {
        background-color: #f8f9fa !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-control-color {
        width: 60px;
        cursor: pointer;
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

           
        </div>
        <div class="col-6">
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

                            <div class="row">
                                <div class="mb-4 col-md-6">
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

                                <div class="mb-4 col-md-6">
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
                            </div>

                            {{-- <div class="mb-4">
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
                            </div> --}}

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                                <button type="button" class="btn btn-secondary" id="resetDefaults">Reset to Defaults</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-6">
            <div class="">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Game Levels</h5>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="resourcesTable">
                                <thead>
                                    <tr>
                                        {{-- <th width="40">Order</th> --}}
                                        <th>Title</th>
                                        <th width="100">Color</th>
                                        <th width="100">Games</th>
                                        <th width="100">Status</th>
                                        {{-- <th width="100">Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resources as $resource)
                                    <tr data-resource-id="{{ $resource->id }}" class="{{ !$resource->is_active ? 'table-secondary' : '' }}">
                                        {{-- <td class="text-center">
                                            <i class="fas fa-grip-vertical drag-handle" style="cursor: move;"></i>
                                        </td> --}}
                                        <td>{{ $resource->title }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div style="width: 30px; height: 30px; background-color: {{ $resource->event_color }}; border-radius: 4px; margin-right: 8px;"></div>
                                                <small>{{ $resource->event_color }}</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $resource->events()->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input resource-toggle" 
                                                    type="checkbox" 
                                                    data-resource-id="{{ $resource->id }}"
                                                    {{ $resource->is_active ? 'checked' : '' }}>
                                                <label class="form-check-label">
                                                    {{ $resource->is_active ? 'Active' : 'Inactive' }}
                                                </label>
                                            </div>
                                        </td>
                                        {{-- <td>
                                            @if($resource->events()->count() == 0)
                                                <button class="btn btn-sm btn-danger delete-resource" 
                                                        data-resource-id="{{ $resource->id }}"
                                                        title="Delete Resource">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled title="Has events">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            * Drag rows to reorder resources. Inactive resources won't appear in the calendar.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Add Resource Modal -->
<div class="modal fade" id="addResourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Resource</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addResourceForm">
                    <div class="mb-3">
                        <label for="resourceTitle" class="form-label">Resource Title</label>
                        <input type="text" class="form-control" id="resourceTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="resourceColor" class="form-label">Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="resourceColor" value="#3788d8">
                            <input type="text" class="form-control" id="resourceColorText" value="#3788d8" pattern="^#[0-9A-Fa-f]{6}$">
                        </div>
                        <small class="text-muted">Click to pick a color or enter hex code</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveResourceBtn">Save Resource</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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


      $("#resourcesTable tbody").sortable({
        handle: ".drag-handle",
        helper: function(e, tr) {
            const originals = tr.children();
            const helper = tr.clone();
            helper.children().each(function(index) {
                $(this).width(originals.eq(index).width());
            });
            return helper;
        },
        update: function(event, ui) {
            const resources = [];
            $("#resourcesTable tbody tr").each(function(index) {
                resources.push({
                    id: $(this).data('resource-id'),
                    order_index: index
                });
            });
            
            $.ajax({
                url: '{{ route("calendar.resources.order") }}',
                method: 'PUT',
                data: { resources: resources },
                success: function() {
                    showToast('Level updated');
                }
            });
        }
    });
    
    // Toggle resource active/inactive
    $('.resource-toggle').change(function() {
        const resourceId = $(this).data('resource-id');
        const isActive = $(this).is(':checked');
        const row = $(this).closest('tr');
        
        $.ajax({
            url: `/api/calendar/resources/${resourceId}`,
            method: 'PUT',
            data: { is_active: isActive },
            success: function() {
                if (isActive) {
                    row.removeClass('table-secondary');
                    row.find('.form-check-label').text('Active');
                } else {
                    row.addClass('table-secondary');
                    row.find('.form-check-label').text('Inactive');
                }
                showToast('Resource status updated');
            },
            error: function() {
                // Revert checkbox if error
                $(this).prop('checked', !isActive);
                showToast('Failed to update resource', 'error');
            }
        });
    });
    
    // Delete resource
    $('.delete-resource').click(function() {
        if (!confirm('Are you sure you want to delete this resource?')) return;
        
        const resourceId = $(this).data('resource-id');
        const row = $(this).closest('tr');
        
        $.ajax({
            url: `/api/calendar/resources/${resourceId}`,
            method: 'DELETE',
            success: function() {
                row.fadeOut(function() {
                    $(this).remove();
                });
                showToast('Resource deleted successfully');
            },
            error: function(xhr) {
                const response = JSON.parse(xhr.responseText);
                showToast(response.message || 'Failed to delete resource', 'error');
            }
        });
    });
    
    // Color picker sync
    $('#resourceColor').on('input', function() {
        $('#resourceColorText').val($(this).val());
    });
    
    $('#resourceColorText').on('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test($(this).val())) {
            $('#resourceColor').val($(this).val());
        }
    });
    
    // Add new resource
    $('#saveResourceBtn').click(function() {
        const title = $('#resourceTitle').val();
        const color = $('#resourceColorText').val();
        
        if (!title || !color) {
            alert('Please fill all fields');
            return;
        }
        
        $.ajax({
            url: '{{ route("calendar.resources.create") }}',
            method: 'POST',
            data: {
                title: title,
                event_color: color
            },
            success: function(response) {
                $('#addResourceModal').modal('hide');
                location.reload(); // Reload to show new resource
            },
            error: function() {
                alert('Failed to create resource');
            }
        });
    });
    
    // Toast notification function
    function showToast(message, type = 'success') {
        // Create toast container if it doesn't exist
        let toastContainer = $('.toast-container');
        if (toastContainer.length === 0) {
            toastContainer = $(`
                <div class="toast-container position-fixed p-3" 
                    style="z-index: 9999; bottom: 0; right: 0;">
                </div>
            `);
            $('body').append(toastContainer);
        }

        // Determine styling based on type
        const typeStyles = {
            success: {
                icon: 'check-circle-fill',
                bgClass: 'bg-success',
                textClass: 'text-white',
                borderClass: 'border-success'
            },
            error: {
                icon: 'exclamation-triangle-fill',
                bgClass: 'bg-danger',
                textClass: 'text-white',
                borderClass: 'border-danger'
            },
            warning: {
                icon: 'exclamation-triangle-fill',
                bgClass: 'bg-warning',
                textClass: 'text-dark',
                borderClass: 'border-warning'
            },
            info: {
                icon: 'info-fill',
                bgClass: 'bg-info',
                textClass: 'text-white',
                borderClass: 'border-info'
            }
        };
        const style = typeStyles[type] || typeStyles.success;

        // Create toast element
        const toast = $(`
            <div class="toast show align-items-center ${style.bgClass} ${style.textClass} ${style.borderClass}" 
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="bi bi-${style.icon} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);

        // Add to container
        toastContainer.append(toast);

        // Auto-remove after delay
        setTimeout(() => {
            toast.removeClass('show');
            toast.addClass('hide');
                setTimeout(() => toast.remove(), 300);
            }, 5000);

            // Allow manual dismissal
            toast.find('.btn-close').on('click', () => {
                toast.removeClass('show');
                toast.addClass('hide');
                setTimeout(() => toast.remove(), 300);
            });
        }
});
</script>
@endpush