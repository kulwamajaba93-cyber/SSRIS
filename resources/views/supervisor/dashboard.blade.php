@extends('layouts.supervisor')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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

    <!-- Performance Status Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Performance Status</h5>
                </div>
                <div class="card-body">
                    @php
                        $user = auth()->user();
                    @endphp
                    @if($user->isPerformanceApproved())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Approved!</strong>
                            @if($user->performance_signed_at)
                                <span class="text-muted d-block">Approved on: {{ $user->performance_signed_at->format('M j, Y') }}</span>
                            @endif
                            @if($user->performance_hod_remarks)
                                <span class="d-block mt-2"><strong>Remarks:</strong> {{ $user->performance_hod_remarks }}</span>
                            @endif
                        </div>
                    @elseif($user->isPerformanceRejected())
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i>
                            <strong>Rejected!</strong>
                            @if($user->performance_signed_at)
                                <span class="text-muted d-block">Rejected on: {{ $user->performance_signed_at->format('M j, Y') }}</span>
                            @endif
                            @if($user->performance_hod_remarks)
                                <span class="d-block mt-2"><strong>Reason for Rejection:</strong> {{ $user->performance_hod_remarks }}</span>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-clock"></i>
                            <strong>Pending Approval</strong>
                            <span class="text-muted d-block">Your performance is pending review by the Head of Department.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalStudents }}</h3>
                    <p class="mb-0 opacity-75">Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-lightbulb fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $conceptNotesCount ?? 0 }}</h3>
                    <p class="mb-0 opacity-75">Concept Notes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $proposalCount ?? 0 }}</h3>
                    <p class="mb-0 opacity-75">Proposals</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-database fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $dataCollectionCount ?? 0 }}</h3>
                    <p class="mb-0 opacity-75">Data Collection</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $reportCount ?? 0 }}</h3>
                    <p class="mb-0 opacity-75">Reports</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalMeetings }}</h3>
                    <p class="mb-0 opacity-75">Meetings</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card h-100 bg-dark text-white">
                <div class="card-body text-center">
                    <i class="fas fa-comments fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalFeedback }}</h3>
                    <p class="mb-0 opacity-75">Feedback</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Students Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Assigned Students</h5>
                </div>
                <div class="card-body">
                    @if($assignedStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Program</th>
                                        <th>Year</th>
                                        <th>Reg Number</th>
                                        <th>Concept Notes</th>
                                        <th>Proposal Status</th>
                                        <th>Data Collection & Analysis</th>
                                        <th>Report Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedStudents as $student)
                                        <tr>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->program }}</td>
                                            <td>{{ $student->year }}</td>
                                            <td>{{ $student->username }}</td>
                                            <td>
                                                @php $hasConceptNotes = $student->proposals->where('document_type', 'concept_notes')->count() > 0; @endphp
                                                @if($hasConceptNotes)
                                                    <span class="badge bg-success">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Submitted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $hasProposal = $student->proposals->where('document_type', 'proposal')->count() > 0; @endphp
                                                @if($hasProposal)
                                                    <span class="badge bg-success">Proposal Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary">No Proposal Submitted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $hasDataCollection = $student->proposals->where('document_type', 'data_collection')->count() > 0; @endphp
                                                @if($hasDataCollection)
                                                    <span class="badge bg-success">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Submitted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $hasReport = $student->proposals->where('document_type', 'report')->count() > 0; @endphp
                                                @if($hasReport)
                                                    <span class="badge bg-success">Submitted</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Submitted</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No students assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Student Documents Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Student Documents</h5>
                </div>
                <div class="card-body">
                    <!-- Document Type Tabs -->
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#concept-notes-tab">Concept Notes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#proposal-tab">Proposal</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#data-collection-tab">Data Collection</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-tab">Report</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Concept Notes Tab -->
                        <div class="tab-pane fade show active" id="concept-notes-tab">
                            @if($conceptNotesDocuments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Title</th>
                                                <th>Version</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($conceptNotesDocuments as $document)
                                                <tr>
                                                    <td>{{ $document->student->name }}</td>
                                                    <td>{{ $document->title }}</td>
                                                    <td><span class="badge bg-secondary">{{ $document->version_display }}</span></td>
                                                    <td><span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span></td>
                                                    <td>{{ $document->created_at->format('M j, Y') }}</td>
                                                    <td>
                                                        @if($document->file_path)
                                                            <a href="{{ route('supervisor.proposals.download', $document) }}" class="btn btn-sm btn-info" title="Download Document">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('supervisor.feedback.create', $document) }}" class="btn btn-sm btn-success">Give Feedback</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No concept notes submitted yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Proposal Tab -->
                        <div class="tab-pane fade" id="proposal-tab">
                            @if($proposalDocuments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Title</th>
                                                <th>Version</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($proposalDocuments as $document)
                                                <tr>
                                                    <td>{{ $document->student->name }}</td>
                                                    <td>{{ $document->title }}</td>
                                                    <td><span class="badge bg-secondary">{{ $document->version_display }}</span></td>
                                                    <td><span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span></td>
                                                    <td>{{ $document->created_at->format('M j, Y') }}</td>
                                                    <td>
                                                        @if($document->file_path)
                                                            <a href="{{ route('supervisor.proposals.download', $document) }}" class="btn btn-sm btn-info" title="Download Document">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('supervisor.feedback.create', $document) }}" class="btn btn-sm btn-success">Give Feedback</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No proposals submitted yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Data Collection Tab -->
                        <div class="tab-pane fade" id="data-collection-tab">
                            @if($dataCollectionDocuments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Title</th>
                                                <th>Version</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dataCollectionDocuments as $document)
                                                <tr>
                                                    <td>{{ $document->student->name }}</td>
                                                    <td>{{ $document->title }}</td>
                                                    <td><span class="badge bg-secondary">{{ $document->version_display }}</span></td>
                                                    <td><span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span></td>
                                                    <td>{{ $document->created_at->format('M j, Y') }}</td>
                                                    <td>
                                                        @if($document->file_path)
                                                            <a href="{{ route('supervisor.proposals.download', $document) }}" class="btn btn-sm btn-info" title="Download Document">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('supervisor.feedback.create', $document) }}" class="btn btn-sm btn-success">Give Feedback</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No data collection documents submitted yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Report Tab -->
                        <div class="tab-pane fade" id="report-tab">
                            @if($reportDocuments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Title</th>
                                                <th>Version</th>
                                                <th>Status</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reportDocuments as $document)
                                                <tr>
                                                    <td>{{ $document->student->name }}</td>
                                                    <td>{{ $document->title }}</td>
                                                    <td><span class="badge bg-secondary">{{ $document->version_display }}</span></td>
                                                    <td><span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span></td>
                                                    <td>{{ $document->created_at->format('M j, Y') }}</td>
                                                    <td>
                                                        @if($document->file_path)
                                                            <a href="{{ route('supervisor.proposals.download', $document) }}" class="btn btn-sm btn-info" title="Download Document">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('supervisor.feedback.create', $document) }}" class="btn btn-sm btn-success">Give Feedback</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No reports submitted yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meetings Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Meetings</h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#createMeetingModal">
                            <i class="fas fa-plus"></i> Create Meeting
                        </button>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#recordMeetingModal">
                            <i class="fas fa-plus"></i> Record Meeting
                        </button>
                    </div>
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

    <!-- Feedback Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Feedback Given</h5>
                </div>
                <div class="card-body">
                    @if($feedback->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Document Type</th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($feedback as $item)
                                        <tr>
                                            <td>{{ $item->student->name }}</td>
                                            <td>
                                                @if($item->proposal)
                                                    @if($item->proposal->document_type === 'proposal')
                                                        <span class="badge bg-primary">Proposal</span>
                                                    @elseif($item->proposal->document_type === 'data_collection')
                                                        <span class="badge bg-success">Data Collection</span>
                                                    @elseif($item->proposal->document_type === 'report')
                                                        <span class="badge bg-warning">Report</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unknown</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">General</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->title ?? 'Feedback' }}</td>
                                            <td><span class="badge bg-{{ $item->priority_badge_color }}">{{ $item->priority_label }}</span></td>
                                            <td><span class="badge bg-{{ $item->status_badge_color }}">{{ $item->status_label }}</span></td>
                                            <td>{{ $item->created_at->format('M j, Y') }}</td>
                                            <td>
                                                <a href="{{ route('supervisor.feedback.show', $item) }}" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No feedback given yet.</p>
                        </div>
                    @endif
                </div>
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
                        <select class="form-select" name="student_ids[]" multiple required>
                            @foreach($assignedStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->username }})</option>
                            @endforeach
                        </select>
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
                    <div class="mb-3">
                        <label class="form-label">Discussion Notes (Optional)</label>
                        <textarea class="form-control" rows="3" name="discussion_notes"></textarea>
                        @error('discussion_notes')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action Points (Optional)</label>
                        <textarea class="form-control" rows="2" name="action_points"></textarea>
                        @error('action_points')
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
                        <select class="form-select" name="student_ids[]" multiple required>
                            @foreach($assignedStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->username }})</option>
                            @endforeach
                        </select>
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
                        <label class="form-label">Meeting Date <span class="text-danger">*</span></label>
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
                    <div class="mb-3">
                        <label class="form-label">Discussion Notes</label>
                        <textarea class="form-control" rows="3" name="discussion_notes"></textarea>
                        @error('discussion_notes')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action Points</label>
                        <textarea class="form-control" rows="2" name="action_points"></textarea>
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
