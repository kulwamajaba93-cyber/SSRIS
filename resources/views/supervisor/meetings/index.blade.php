@extends('layouts.supervisor')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('supervisor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Meetings</li>
    </ol>
</nav>

<!-- User Info Bar -->
<div class="supervisor-info-bar">
    <div class="supervisor-info-item">
        <i class="fas fa-user"></i>
        <span>Logged in as: {{ strtoupper(auth()->user()->name) }}</span>
    </div>
    <div class="supervisor-info-item">
        <i class="fas fa-calendar"></i>
        <span>Academic Year: 2025/2026</span>
    </div>
    <div class="supervisor-info-item">
        <i class="fas fa-clock"></i>
        <span>{{ now()->format('l jS \\of F, Y') }}</span>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Meetings</h1>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                    <i class="fas fa-plus"></i> Create Meeting
                </button>
                <button type="button" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#recordMeetingModal">
                    <i class="fas fa-plus"></i> Record Meeting
                </button>
                <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Meeting History</h5>
            </div>
            <div class="card-body">
                @if($meetings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Students</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($meetings as $meeting)
                                    <tr>
                                        <td>
                                            @if($meeting->students->count() > 0)
                                                {{ $meeting->students->pluck('name')->join(', ') }}
                                            @elseif($meeting->student)
                                                {{ $meeting->student->name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $meeting->title ?? 'N/A' }}</td>
                                        <td>{{ $meeting->meeting_date ? $meeting->meeting_date->format('M j, Y g:i A') : 'N/A' }}</td>
                                        <td><span class="badge bg-{{ $meeting->status_badge_color }}">{{ $meeting->status_label }}</span></td>
                                        <td>
                                            @if($meeting->meeting_url)
                                                <a href="{{ $meeting->meeting_url }}" target="_blank" class="btn btn-sm btn-success me-1">
                                                    <i class="fas fa-video"></i> Join Meet
                                                </a>
                                            @endif
                                            <a href="{{ route('supervisor.meetings.show', $meeting) }}" class="btn btn-sm btn-primary">View Details</a>
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
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Meeting Modal -->
<div class="modal fade" id="createMeetingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('supervisor.meetings.store') }}">
                @csrf
                <input type="hidden" name="action_type" value="create">
                <div class="modal-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Students <span class="text-danger">*</span></label>
                        <div class="border rounded p-3 bg-light">
                            @foreach($assignedStudents as $student)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student-{{ $student->id }}">
                                    <label class="form-check-label" for="student-{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->reg_number }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('student_ids')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meeting Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="meeting_date" required>
                        @error('meeting_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Meet URL <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="meeting_url" placeholder="meet.google.com/qut-svvo-msq" required>
                        @error('meeting_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Record Meeting Modal -->
<div class="modal fade" id="recordMeetingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Meeting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('supervisor.meetings.store') }}">
                @csrf
                <input type="hidden" name="action_type" value="record">
                <div class="modal-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Student <span class="text-danger">*</span></label>
                        <select class="form-select" name="student_id" required>
                            <option value="">Select a student</option>
                            @foreach($assignedStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->reg_number }})</option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meeting Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meeting Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="meeting_date" required>
                        @error('meeting_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discussion Notes</label>
                        <textarea class="form-control" name="discussion_notes" rows="4" placeholder="Key points discussed..."></textarea>
                        @error('discussion_notes')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action Points</label>
                        <textarea class="form-control" name="action_points" rows="3" placeholder="Next steps for the student..."></textarea>
                        @error('action_points')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Meeting</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
