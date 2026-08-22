@extends('layouts.student')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.meetings.index') }}">My Meetings</a></li>
        <li class="breadcrumb-item active" aria-current="page">Meeting Details</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Meeting Details</h1>
    <div class="d-flex gap-2">
        @if($meeting->meeting_url)
            <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-success">
                <i class="fas fa-video"></i> Join Google Meet
            </a>
        @endif
        <a href="{{ route('student.meetings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Meetings
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-calendar"></i> Meeting Information</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Date & Time:</strong>
                <p>{{ $meeting->meeting_date ? $meeting->meeting_date->format('M j, Y g:i A') : 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <strong>Status:</strong>
                <p><span class="badge bg-{{ $meeting->status_badge_color }}">{{ $meeting->status_label }}</span></p>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Supervisor:</strong>
                <p>{{ $meeting->supervisor->name }}</p>
            </div>
        </div>
        
        <div class="mb-3">
            <strong>Title:</strong>
            <p>{{ $meeting->title ?? 'N/A' }}</p>
        </div>
        
        @if($meeting->meeting_url)
            <div class="mb-3">
                <strong>Meeting Link:</strong>
                <p><a href="{{ $meeting->meeting_url }}" target="_blank">{{ $meeting->meeting_url }}</a></p>
            </div>
        @endif

        <hr>

        <div class="mb-3">
            <h5><i class="fas fa-sticky-note"></i> Discussion Notes</h5>
            <div class="p-3 bg-light rounded">
                @if($meeting->discussion_notes)
                    <p>{{ $meeting->discussion_notes }}</p>
                @else
                    <p class="text-muted">No discussion notes recorded.</p>
                @endif
            </div>
        </div>

        <div class="mb-3">
            <h5><i class="fas fa-tasks"></i> Action Points</h5>
            <div class="p-3 bg-light rounded">
                @if($meeting->action_points)
                    <p style="white-space: pre-line;">{{ $meeting->action_points }}</p>
                @else
                    <p class="text-muted">No action points recorded.</p>
                @endif
            </div>
        </div>

        @if($meeting->supervisor_notes)
            <div class="mb-3">
                <h5><i class="fas fa-user-tie"></i> Supervisor Notes</h5>
                <div class="p-3 bg-light rounded">
                    <p>{{ $meeting->supervisor_notes }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
