<script>
$(document).ready(function() {
    // CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    let calendar;
    let currentEvent = null;


    let calendarSettings = {};

    // Load settings first
    $.ajax({
        url: '{{ route("calendar.settings.get") }}',
        async: false, // Wait for settings before initializing calendar
        success: function(settings) {
            calendarSettings = settings;
        }
    });
    
    // Initialize calendar
    initializeCalendar();
    
    // Load unscheduled events
    loadUnscheduledEvents();
    
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            initialView: 'resourceTimeGridDay',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimeGridWeek,resourceTimeGridDay,dayGridMonth'
            },
            editable: true,
            droppable: true,
            eventResizableFromStart: true,
            selectable: true,
            nowIndicator: true,
            dayMaxEvents: true,
            height: 700,

            slotDuration: calendarSettings.slotDuration,
            slotLabelInterval: calendarSettings.slotLabelInterval,
            snapDuration: calendarSettings.snapDuration,
            slotMinTime: calendarSettings.slotMinTime,
            slotMaxTime: calendarSettings.slotMaxTime,
             slotLabelFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false // Use 24-hour format
                },
                eventTimeFormat: { // 24-hour format for events
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            
            resources: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '{{ route("calendar.resources") }}',
                    success: function(data) {
                        successCallback(data);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            // Load events from API
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '{{ route("calendar.events") }}',
                    data: {
                        start: fetchInfo.start.toISOString(),
                        end: fetchInfo.end.toISOString()
                    },
                    success: function(data) {
                        successCallback(data);
                    },
                    error: function() {
                        failureCallback();
                    }
                });
            },
            
            // Handle external event drop
            eventReceive: function(info) {
                const eventId = info.draggedEl.getAttribute('data-event-id');
                const eventData = JSON.parse(info.draggedEl.getAttribute('data-event'));
                
                // Remove the temporary event
                info.event.remove();
                
                $.ajax({
                    url: `/api/calendar/events/${eventId}/schedule`,
                    method: 'POST',
                    data: {
                        event_id: eventId,
                        start: info.event.start.toISOString(),
                        end: info.event.end.toISOString(),
                        resource_id: info.event.getResources()[0].id
                    },
                    success: function(response) {
                        // Remove from unscheduled list
                        info.draggedEl.remove();
                        
                        // Add the event with server data INCLUDING original backgroundColor
                        calendar.addEvent(response.event);
                        
                        showToast('Event scheduled successfully');
                    },
                    error: function() {
                        // If error, reload events to ensure consistency
                        calendar.refetchEvents();
                        loadUnscheduledEvents();
                        showToast('Failed to schedule event', 'error');
                    }
                });
            },

            eventContent: function(arg) {
                const levelName = arg.event.extendedProps.level_name;
                const originalTitle = arg.event.extendedProps.original_title || arg.event.title;
                const timeText = arg.timeText; // This will show the time range
                
                return {
                    html: `
                        <div class="fc-event-main-frame">
                            <div class="fc-event-time" style="font-size: 0.75em; margin-bottom: 2px;">
                                ${timeText}
                            </div>
                            <div class="fc-event-title-wrap">
                                <div class="fc-event-title fc-sticky">
                                    ${originalTitle}
                                </div>
                            </div>
                             ${levelName ? `
                            <div class="fc-event-level-tag" style="font-size: 0.75em; font-weight: bold;">
                                ${levelName}
                            </div>
                            ` : ''}
                        </div>
                    `
                };
            },
                        
            // Handle event click
            eventClick: function(info) {
                currentEvent = info.event;
                showEventDetails(info.event);
            },
            
            // Handle event drop (reschedule)
            eventDrop: function(info) {
                updateEventSchedule(info.event, info.revert);
            },
            
            // Handle event resize
            eventResize: function(info) {
                updateEventSchedule(info.event, info.revert);
            },
            
            // Handle drag to unscheduled area
            eventDragStop: function(info) {
                const trashEl = document.getElementById('external-events');
                const trashRect = trashEl.getBoundingClientRect();
                const x = info.jsEvent.clientX;
                const y = info.jsEvent.clientY;
                
                if (x >= trashRect.left && x <= trashRect.right && 
                    y >= trashRect.top && y <= trashRect.bottom) {
                    
                    unscheduleEvent(info.event.id);
                }
            }
        });
        
        calendar.render();
    }
    
    function loadUnscheduledEvents() {
        $.ajax({
            url: '{{ route("calendar.unscheduled") }}',
            success: function(events) {
                const list = $('#unscheduled-list');
                list.empty();
                
                events.forEach(function(event) {
                    addUnscheduledEventToList(event);
                });
                
                // Initialize draggable
                initializeDraggable();
            }
        });
    }
    
    function addUnscheduledEventToList(event) {
        const duration = parseDuration(event.extendedProps.duration);
        const eventHtml = `
            <div class="external-event" style="background-color:${event.backgroundColor}" data-event-id="${event.id}" data-event='${JSON.stringify(event)}'>
                <div>${event.title}</div>
                <div>${event.extendedProps.level_name}</div>
                <div class="event-meta">Duration: ${duration}</div>
            </div>
        `;
        $('#unscheduled-list').append(eventHtml);
    }
    
    function initializeDraggable() {
        const containerEl = document.getElementById('external-events');
        new FullCalendar.Draggable(containerEl, {
            itemSelector: '.external-event',
            eventData: function(eventEl) {
                const eventData = JSON.parse(eventEl.getAttribute('data-event'));                
                return {
                    id: eventData.id,
                    title: eventData.title,
                    backgroundColor: eventData.backgroundColor,
                    borderColor: eventData.backgroundColor,
                    duration: eventData.extendedProps.duration || '01:30',
                    extendedProps: eventData.extendedProps || {}
                };
            }
        });
    }
    
    function updateEventSchedule(event, revertFunc) {
        const resources = event.getResources();
        
        $.ajax({
            url: `/api/calendar/events/${event.id}`,
            method: 'POST',
            data: {
                start: event.start.toISOString(),
                end: event.end.toISOString(),
                resource_id: resources.length > 0 ? resources[0].id : null
            },
            success: function() {
                showToast('Game updated successfully');
            },
            error: function() {
                revertFunc();
                showToast('Failed to update Game', 'error');
            }
        });
    }
    
    function unscheduleEvent(eventId) {
        $.ajax({
            url: `/api/calendar/events/${eventId}/unschedule`,
            method: 'POST',
            success: function() {
                const event = calendar.getEventById(eventId);
                if (event) {
                    event.remove();
                }
                loadUnscheduledEvents();
                $('#eventModal').modal('hide');
                showToast('Game unscheduled successfully');
            },
            error: function() {
                showToast('Failed to unschedule event', 'error');
            }
        });
    }
    
    window.deleteEvent = function(eventId) {
        if (confirm('Are you sure you want to delete this event?')) {
            $.ajax({
                url: `/api/calendar/events/${eventId}`,
                method: 'DELETE',
                success: function() {
                    $(`.external-event[data-event-id="${eventId}"]`).remove();
                    showToast('Event deleted successfully');
                },
                error: function() {
                    showToast('Failed to delete event', 'error');
                }
            });
        }
    };
    
    function showEventDetails(event) {
        $('#eventModalTitle').text(event.title);
        
        const resources = event.getResources();
        const modalBody = `
            <dl>
                <dt>Start:</dt>
                <dd>${event.start.toLocaleString()}</dd>
                <dt>End:</dt>
                <dd>${event.end ? event.end.toLocaleString() : 'N/A'}</dd>
                <dt>Group:</dt>
                <dd>${resources.map(r => r.title).join(', ')}</dd>
                <dt>Field:</dt>
                <dd>${event.extendedProps.field || 'Not specified'}</dd>
                <dt>Referee:</dt>
                <dd>${event.extendedProps.referee || 'Not assigned'}</dd>
                <dt>Level:</dt>
                <dd>${event.extendedProps.level_name || 'Not assigned'}</dd>
                <dt>Notes:</dt>
                <dd>${event.extendedProps.notes || 'No additional notes'}</dd>
            </dl>
        `;
        
        $('#eventModalBody').html(modalBody);
        $('#eventModal').modal('show');
    }
    
    // Handle unschedule button click
    $('#unscheduleBtn').click(function() {
        if (currentEvent) {
            unscheduleEvent(currentEvent.id);
        }
    });
    
    // Handle add event form
    $('#saveEventBtn').click(function() {
        const form = $('#addEventForm');
        if (form[0].checkValidity()) {
            const data = {
                title: $('#eventTitle').val(),
                field: $('#eventField').val() || 'TBD',
                referee: $('#eventReferee').val() || 'TBD',
                notes: $('#eventNotes').val(),
                duration: $('#eventDuration').val()
            };
            
            $.ajax({
                url: '{{ route("calendar.create") }}',
                method: 'POST',
                data: data,
                success: function(response) {
                    addUnscheduledEventToList(response.event);
                    initializeDraggable();
                    $('#addEventModal').modal('hide');
                    form[0].reset();
                    showToast('Event created successfully');
                },
                error: function() {
                    showToast('Failed to create event', 'error');
                }
            });
        }
    });
    
    function parseDuration(duration) {
        const parts = duration.split(':');
        const hours = parseInt(parts[0]);
        const minutes = parseInt(parts[1]);
        
        if (hours === 1 && minutes === 0) return '1 hour';
        if (hours === 1 && minutes === 30) return '1.5 hours';
        if (hours === 2 && minutes === 0) return '2 hours';
        
        return `${hours}h ${minutes}m`;
    }

    $('#levelFilter').change(function() {
        const selectedLevel = $(this).val();
        
        if (selectedLevel) {
            // Hide all events first
            $('.external-event').hide();
            
            // Show only events with selected level
            $('.external-event').each(function() {
                const eventData = JSON.parse($(this).attr('data-event'));
                if (eventData.extendedProps && eventData.extendedProps.level_id == selectedLevel) {
                    $(this).show();
                }
            });
        } else {
            // Show all events
            $('.external-event').show();
        }
        
        // Update count
        const visibleCount = $('.external-event:visible').length;
        $('#unscheduled-count').text(`(${visibleCount} matches)`);
    });


    
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