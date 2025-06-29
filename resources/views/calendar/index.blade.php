{{-- resources/views/calendar/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tournament Calendar')

@push('styles')
    @include('calendar.style')
@endpush

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Calendar Column -->
        <div class="col-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Tournament Schedule</h3> 
                <a href="{{ route('calendar.settings') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-cog"></i> Time Settings
                </a>
            </div>
            <div id="calendar"></div>
        </div>
        
        <!-- Unscheduled Events Column -->
        <div class="col-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Unscheduled Matches</h3>
                {{-- <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    + Add
                </button> --}}
            </div>
            <div id="external-events">
                <div class="unscheduled-header">Drag matches to schedule them</div>
                <div id="unscheduled-list">
                    <!-- Unscheduled events will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="unscheduleBtn">Unschedule Event</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Match</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">Match Title</label>
                        <input type="text" class="form-control" id="eventTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventField" class="form-label">Field</label>
                        <input type="text" class="form-control" id="eventField" placeholder="TBD">
                    </div>
                    <div class="mb-3">
                        <label for="eventReferee" class="form-label">Referee</label>
                        <input type="text" class="form-control" id="eventReferee" placeholder="TBD">
                    </div>
                    <div class="mb-3">
                        <label for="eventNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="eventNotes" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="eventDuration" class="form-label">Duration</label>
                        <select class="form-control" id="eventDuration">
                            <option value="01:00:00">1 hour</option>
                            <option value="01:30:00" selected>1.5 hours</option>
                            <option value="02:00:00">2 hours</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEventBtn">Save Match</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('calendar.script')
@endpush