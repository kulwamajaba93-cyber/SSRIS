@extends('layouts.supervisor')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('supervisor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            @if($documentType === 'concept_notes')
                Concept Notes
            @elseif($documentType === 'data_collection')
                Data Collection and Analysis
            @elseif($documentType === 'report')
                Reports
            @else
                Proposals
            @endif
        </li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                @if($documentType === 'concept_notes')
                    Concept Notes
                @elseif($documentType === 'data_collection')
                    Data Collection and Analysis Documents
                @elseif($documentType === 'report')
                    Report Documents
                @else
                    Student Proposals
                @endif
            </h1>
            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header @if($documentType === 'concept_notes') bg-info @elseif($documentType === 'data_collection') bg-success @elseif($documentType === 'report') bg-warning @else bg-primary @endif text-white">
                <h5 class="mb-0">
                    @if($documentType === 'concept_notes')
                        <i class="fas fa-lightbulb"></i> Concept Notes
                    @elseif($documentType === 'data_collection')
                        <i class="fas fa-database"></i> Data Collection and Analysis
                    @elseif($documentType === 'report')
                        <i class="fas fa-book"></i> Reports
                    @else
                        <i class="fas fa-file-alt"></i> Proposals
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($proposals->count() > 0)
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
                                @foreach($proposals as $proposal)
                                    <tr>
                                        <td>{{ $proposal->student->name }}</td>
                                        <td>{{ $proposal->title }}</td>
                                        <td><span class="badge bg-secondary">{{ $proposal->version_display }}</span></td>
                                        <td><span class="badge bg-{{ $proposal->status_badge_color }}">{{ $proposal->status_label }}</span></td>
                                        <td>{{ $proposal->created_at->format('M j, Y') }}</td>
                                        <td>
                                            @if($proposal->file_path)
                                                <a href="{{ route('supervisor.proposals.download', $proposal) }}" class="btn btn-sm btn-info" title="Download Document">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @endif
                                            <a href="{{ route('supervisor.feedback.create', $proposal) }}" class="btn btn-sm btn-success">Give Feedback</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        @if($documentType === 'concept_notes')
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No concept notes submitted yet.</p>
                        @elseif($documentType === 'data_collection')
                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No data collection documents submitted yet.</p>
                        @elseif($documentType === 'report')
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No reports submitted yet.</p>
                        @else
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No proposals submitted yet.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
