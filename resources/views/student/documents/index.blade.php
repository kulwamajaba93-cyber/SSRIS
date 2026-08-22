@extends('layouts.student')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">My Document</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            @if($documentType === 'concept_notes')
                Concept Notes
            @elseif($documentType === 'proposal')
                Proposal
            @elseif($documentType === 'data_collection')
                Data Collection and Analysis
            @elseif($documentType === 'report')
                Report
            @else
                All Documents
            @endif
        </li>
    </ol>
</nav>

<!-- User Info Bar -->
<div class="student-info-bar">
    <div class="student-info-item">
        <i class="fas fa-user"></i>
        <span>Logged in as: {{ strtoupper(auth()->user()->name) }}</span>
    </div>
    <div class="student-info-item">
        <i class="fas fa-calendar"></i>
        <span>Academic Year: 2025/2026</span>
    </div>
    <div class="student-info-item">
        <i class="fas fa-clock"></i>
        <span>{{ now()->format('l jS \\of F, Y') }}</span>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>
                @if($documentType === 'concept_notes')
                    My Concept Notes
                @elseif($documentType === 'proposal')
                    My Proposals
                @elseif($documentType === 'data_collection')
                    Data Collection and Analysis
                @elseif($documentType === 'report')
                    My Reports
                @else
                    All Documents
                @endif
            </h1>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadProposalModal">
                    <i class="fas fa-upload"></i> Submit Document
                </button>
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Document Type Tabs -->
<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ !$documentType ? 'active' : '' }}" href="{{ route('student.documents.index') }}">All Documents</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $documentType === 'concept_notes' ? 'active' : '' }}" href="{{ route('student.documents.index', ['type' => 'concept_notes']) }}">Concept Notes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $documentType === 'proposal' ? 'active' : '' }}" href="{{ route('student.documents.index', ['type' => 'proposal']) }}">Proposal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $documentType === 'data_collection' ? 'active' : '' }}" href="{{ route('student.documents.index', ['type' => 'data_collection']) }}">Data Collection</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $documentType === 'report' ? 'active' : '' }}" href="{{ route('student.documents.index', ['type' => 'report']) }}">Report</a>
            </li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Document History</h5>
            </div>
            <div class="card-body">
                @if($documents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Version</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $document)
                                    <tr>
                                        <td>
                                            @if($document->document_type === 'concept_notes')
                                                <span class="badge bg-info">Concept Notes</span>
                                            @elseif($document->document_type === 'proposal')
                                                <span class="badge bg-primary">Proposal</span>
                                            @elseif($document->document_type === 'data_collection')
                                                <span class="badge bg-success">Data Collection</span>
                                            @elseif($document->document_type === 'report')
                                                <span class="badge bg-warning">Report</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $document->document_type }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $document->version_display }}</span></td>
                                        <td>{{ $document->title }}</td>
                                        <td><span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span></td>
                                        <td>{{ $document->submitted_at ? $document->submitted_at->format('M j, Y g:i A') : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('student.documents.show', $document) }}" class="btn btn-sm btn-primary">View</a>
                                            @if($document->file_path)
                                                <a href="{{ route('student.documents.download', $document) }}" class="btn btn-sm btn-success">Download</a>
                                            @endif
                                            @if(!$document->isReviewed())
                                                <form action="{{ route('student.documents.destroy', $document) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this document?')">Remove</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">
                            @if($documentType === 'concept_notes')
                                No concept notes submitted yet.
                            @elseif($documentType === 'proposal')
                                No proposals submitted yet.
                            @elseif($documentType === 'data_collection')
                                No data collection documents submitted yet.
                            @elseif($documentType === 'report')
                                No reports submitted yet.
                            @else
                                No documents submitted yet.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadProposalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Research Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('student.documents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="documentType" class="form-label">Document Type</label>
                        <select class="form-select" id="documentType" name="document_type" required>
                            <option value="concept_notes">Concept Notes</option>
                            <option value="proposal">Proposal</option>
                            <option value="data_collection">Data Collection and Analysis</option>
                            <option value="report">Report</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="proposalTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="proposalTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="proposalDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="proposalDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="proposalFile" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="proposalFile" name="file" accept=".pdf,.doc,.docx" required>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
