@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Meeting Details</h1>
                <div class="d-flex gap-2">
                    @if($meeting->meeting_url)
                        <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-success">
                            <i class="fas fa-video"></i> Join Meet
                        </a>
                    @endif
                    <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Meeting Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-{{ $meeting->status_badge_color }}">{{ $meeting->status_label }}</span>
                    </div>
                    
                    <h6>Students</h6>
                    <p>
                        @if($meeting->students->count() > 0)
                            {{ $meeting->students->pluck('name')->join(', ') }}
                        @elseif($meeting->student)
                            {{ $meeting->student->name }}
                        @else
                            N/A
                        @endif
                    </p>
                    
                    <h6>Title</h6>
                    <p>{{ $meeting->title ?? 'N/A' }}</p>
                    
                    <h6>Meeting Date</h6>
                    <p>{{ $meeting->meeting_date ? $meeting->meeting_date->format('M j, Y g:i A') : 'N/A' }}</p>
                    
                    @if($meeting->meeting_url)
                        <h6>Meeting Link</h6>
                        <p><a href="{{ $meeting->meeting_url }}" target="_blank">{{ $meeting->meeting_url }}</a></p>
                    @endif
                    
                    @if($meeting->discussion_notes)
                        <h6>Discussion Notes</h6>
                        <p>{{ $meeting->discussion_notes }}</p>
                    @endif

                    @if($meeting->action_points)
                        <div class="alert alert-info">
                            <h6><i class="fas fa-tasks me-2"></i>Action Points</h6>
                            <p>{{ $meeting->action_points }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Meeting Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Students:</strong></td>
                            <td>
                                @if($meeting->students->count() > 0)
                                    {{ $meeting->students->pluck('name')->join(', ') }}
                                @elseif($meeting->student)
                                    {{ $meeting->student->name }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Supervisor:</strong></td>
                            <td>{{ $meeting->supervisor->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td>{{ $meeting->meeting_date ? $meeting->meeting_date->format('M j, Y g:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><span class="badge bg-{{ $meeting->status_badge_color }}">{{ $meeting->status_label }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('supervisor.meetings.edit', $meeting) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit me-2"></i> Edit Meeting
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
