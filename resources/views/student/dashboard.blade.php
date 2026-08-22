@extends('layouts.student')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>

<!-- User Info Bar -->
<div class="student-info-bar">
    <div class="student-info-item">
        <i class="fas fa-user"></i>
        <span>Logged in as: {{ strtoupper($student->name) }}</span>
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

<!-- Profile Info Section -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-user"></i> Profile Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Registration Number:</strong></td>
                        <td>{{ $student->username }}</td>
                    </tr>
                    <tr>
                        <td><strong>Program:</strong></td>
                        <td>{{ $student->program ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Year:</strong></td>
                        <td>{{ $student->year ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone Number:</strong></td>
                        <td>{{ $student->phone ?? 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Assigned Supervisor:</strong></td>
                        <td>
                            @if($supervisor)
                                {{ $supervisor->name }} ({{ $supervisor->email }})
                            @else
                                <span class="text-muted">Not assigned</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Document Progress Section -->
<div class="row mb-4">
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
        <div class="card h-100 bg-primary text-white">
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
    <div class="col-md-3 mb-3 mb-md-0">
        <div class="card h-100 bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x mb-2 opacity-75"></i>
                <h3 class="mb-0">{{ $reportCount ?? 0 }}</h3>
                <p class="mb-0 opacity-75">Reports</p>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card h-100 bg-secondary text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-3x mb-2 opacity-75"></i>
                <h3 class="mb-0">{{ $proposals->where('status', 'approved')->count() }}</h3>
                <p class="mb-0 opacity-75">Approved</p>
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
        <div class="card h-100 @if($pendingFeedback > 0) bg-danger @else bg-dark @endif text-white">
            <div class="card-body text-center">
                <i class="fas fa-comments fa-3x mb-2 opacity-75"></i>
                <h3 class="mb-0">
                    @if($pendingFeedback > 0)
                        {{ $pendingFeedback }} / {{ $totalFeedback }}
                    @else
                        {{ $totalFeedback }}
                    @endif
                </h3>
                <p class="mb-0 opacity-75">Feedback</p>
                @if($pendingFeedback > 0)
                    <small class="d-block mt-1 opacity-90">{{ $pendingFeedback }} pending</small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- My Document Section -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-file-alt"></i> My Research Document</h5>
    </div>
    <div class="card-body">
        <!-- Concept Notes Section -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-lightbulb text-info me-2"></i> Concept Notes</h6>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="openUploadModal('concept_notes')">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
            <div class="border rounded p-3 bg-light" id="concept-notes-dropzone">
                @php $conceptNotesDocs = $proposals->where('document_type', 'concept_notes'); @endphp
                @if($conceptNotesDocs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach($conceptNotesDocs as $doc)
                                    <tr>
                                        <td>{{ $doc->title }}</td>
                                        <td><span class="badge bg-{{ $doc->status_badge_color }}">{{ $doc->status_label }}</span></td>
                                        <td>{{ $doc->submitted_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ route('student.documents.show', $doc) }}" class="btn btn-sm btn-primary">View</a>
                                            <a href="{{ route('student.documents.download', $doc) }}" class="btn btn-sm btn-success">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No concept notes uploaded yet. Click Upload to add your first concept notes.</p>
                @endif
            </div>
        </div>

        <!-- Proposal Section -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i> Proposal</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openUploadModal('proposal')">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
            <div class="border rounded p-3 bg-light" id="proposal-dropzone">
                @php $proposalDocs = $proposals->where('document_type', 'proposal'); @endphp
                @if($proposalDocs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach($proposalDocs as $doc)
                                    <tr>
                                        <td>{{ $doc->title }}</td>
                                        <td><span class="badge bg-{{ $doc->status_badge_color }}">{{ $doc->status_label }}</span></td>
                                        <td>{{ $doc->submitted_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ route('student.documents.show', $doc) }}" class="btn btn-sm btn-primary">View</a>
                                            <a href="{{ route('student.documents.download', $doc) }}" class="btn btn-sm btn-success">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No proposals uploaded yet. Click Upload to add your first proposal.</p>
                @endif
            </div>
        </div>

        <!-- Data Collection Section -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-database text-success me-2"></i> Data Collection and Analysis</h6>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="openUploadModal('data_collection')">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
            <div class="border rounded p-3 bg-light" id="data-collection-dropzone">
                @php $dataCollectionDocs = $proposals->where('document_type', 'data_collection'); @endphp
                @if($dataCollectionDocs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach($dataCollectionDocs as $doc)
                                    <tr>
                                        <td>{{ $doc->title }}</td>
                                        <td><span class="badge bg-{{ $doc->status_badge_color }}">{{ $doc->status_label }}</span></td>
                                        <td>{{ $doc->submitted_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ route('student.documents.show', $doc) }}" class="btn btn-sm btn-primary">View</a>
                                            <a href="{{ route('student.documents.download', $doc) }}" class="btn btn-sm btn-success">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No data collection documents uploaded yet. Click Upload to add your first document.</p>
                @endif
            </div>
        </div>

        <!-- Report Section -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-book text-warning me-2"></i> Report</h6>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="openUploadModal('report')">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
            <div class="border rounded p-3 bg-light" id="report-dropzone">
                @php $reportDocs = $proposals->where('document_type', 'report'); @endphp
                @if($reportDocs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach($reportDocs as $doc)
                                    <tr>
                                        <td>{{ $doc->title }}</td>
                                        <td><span class="badge bg-{{ $doc->status_badge_color }}">{{ $doc->status_label }}</span></td>
                                        <td>{{ $doc->submitted_at->format('M j, Y') }}</td>
                                        <td>
                                            <a href="{{ route('student.documents.show', $doc) }}" class="btn btn-sm btn-primary">View</a>
                                            <a href="{{ route('student.documents.download', $doc) }}" class="btn btn-sm btn-success">Download</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No reports uploaded yet. Click Upload to add your first report.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function openUploadModal(documentType) {
    document.getElementById('documentType').value = documentType;
    var modal = new bootstrap.Modal(document.getElementById('uploadProposalModal'));
    modal.show();
}

// Drag and drop functionality
document.querySelectorAll('[id$="-dropzone"]').forEach(function(dropzone) {
    dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary', 'border-2');
    });
    
    dropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'border-2');
    });
    
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'border-2');
        var documentType = this.id.replace('-dropzone', '');
        openUploadModal(documentType);
    });
});
</script>

<!-- Feedback Section -->
<div class="card mb-4">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-comments"></i> Supervisor Feedback</h5>
        @if($pendingFeedback > 0)
            <span class="badge bg-danger">{{ $pendingFeedback }} Pending</span>
        @endif
    </div>
    <div class="card-body">
        @if($feedback->count() > 0)
            <div class="accordion" id="feedbackAccordion">
                @foreach($feedback as $index => $item)
                    <div class="accordion-item @if($item->status === 'pending') border-warning @endif">
                        <h2 class="accordion-header" id="heading{{ $index }}">
                            <button class="accordion-button collapsed @if($item->status === 'pending') text-warning @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                @if($item->status === 'pending')
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                @endif
                                <span class="badge bg-{{ $item->priority_badge_color }} me-2">{{ $item->priority_label }}</span>
                                <span class="badge bg-{{ $item->status_badge_color }} me-2">{{ $item->status_label }}</span>
                                {{ $item->title ?? 'Feedback' }} - {{ $item->created_at->format('M j, Y') }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#feedbackAccordion">
                            <div class="accordion-body">
                                @if($item->status === 'pending')
                                    <div class="alert alert-warning">
                                        <strong><i class="fas fa-bell"></i> New Feedback - Please review and take action</strong>
                                    </div>
                                @endif
                                <p>{{ $item->comments }}</p>
                                @if($item->action_required)
                                    <div class="alert alert-info">
                                        <strong>Action Required:</strong> {{ $item->action_required }}
                                    </div>
                                @endif
                                <small class="text-muted">From: {{ $item->supervisor->name }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <p class="text-muted">No feedback received yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Meeting History Section -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-calendar"></i> Meeting History</h5>
        @if($meetings->count() > 0)
            <a href="{{ route('student.meetings.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-list"></i> View All Meetings
            </a>
        @endif
    </div>
    <div class="card-body">
        @if($meetings->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Action Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($meetings->take(5) as $meeting)
                            <tr>
                                <td>{{ $meeting->meeting_date ? $meeting->meeting_date->format('M j, Y g:i A') : 'N/A' }}</td>
                                <td><span class="badge bg-{{ $meeting->type_badge_color }}">{{ $meeting->type_label }}</span></td>
                                <td><span class="badge bg-{{ $meeting->status_badge_color }}">{{ $meeting->status_label }}</span></td>
                                <td>{{ Str::limit($meeting->discussion_notes ?? 'N/A', 50) }}</td>
                                <td>{{ Str::limit($meeting->action_points ?? 'N/A', 50) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($meetings->count() > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('student.meetings.index') }}" class="btn btn-outline-secondary btn-sm">
                        View All {{ $meetings->count() }} Meetings
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                <p class="text-muted">No meetings recorded yet.</p>
                <p class="text-muted">Your supervisor will record meetings after each session.</p>
            </div>
        @endif
    </div>
</div>

<!-- Upload Proposal Modal -->
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
