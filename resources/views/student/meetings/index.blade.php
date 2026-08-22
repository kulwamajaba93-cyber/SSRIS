@extends('layouts.student')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">My Meetings</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>My Meetings</h1>
    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-calendar"></i> Meeting History</h5>
    </div>
    <div class="card-body">
        @if($meetings->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Supervisor Name</th>
                            <th>Title</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings as $meeting)
                            <tr>
                                <td>{{ $meeting->supervisor->name }}</td>
                                <td>{{ $meeting->title ?? 'N/A' }}</td>
                                <td>{{ $meeting->meeting_date ? $meeting->meeting_date->format('M j, Y g:i A') : 'N/A' }}</td>
                                <td><span class="badge bg-{{ $meeting->status_badge_color }}">{{ $meeting->status_label }}</span></td>
                                <td>
                                    <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-sm btn-success me-1">
                                        <i class="fas fa-video"></i> Join Google Meet
                                    </a>
                                    <a href="{{ route('student.meetings.show', $meeting) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                <p class="text-muted">No meetings recorded yet.</p>
                <p class="text-muted">Your supervisor will create or record meetings for you.</p>
            </div>
        @endif
    </div>
</div>
@endsection
