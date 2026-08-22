<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SSRIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 0.2rem 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            padding: 2rem;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .supervisor-card {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .status-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, #007bff, #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        .supervisor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .program-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 25px;
        }
        .info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        .quick-action-btn {
            padding: 1rem;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .proposal-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .proposal-item:hover {
            transform: translateY(-2px);
        }
        .meeting-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .meeting-item:hover {
            transform: translateY(-2px);
        }
        .feedback-item {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .feedback-item:hover {
            transform: translateY(-2px);
        }
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        .upload-area:hover {
            border-color: #0056b3;
            background-color: #f8f9ff;
        }
        .upload-area.dragover {
            border-color: #0056b3;
            background-color: #e3f2fd;
        }
        .modal-content {
            border-radius: 15px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4>SSRIS</h4>
                        <small>Student Panel</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="{{ route('student.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-chalkboard-teacher me-2"></i> My Supervisor
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-calendar-alt me-2"></i> Schedule
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-comments me-2"></i> Messages
                        </a>
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-alt me-2"></i> Reports
                        </a>
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                        <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2>Student Dashboard</h2>
                            <p class="text-muted mb-0">Welcome back, {{ $user->name }}!</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $user->name }}</div>
                                <small class="text-muted">Student</small>
                            </div>
                        </div>
                    </div>

                    <!-- A. STUDENT PROFILE SUMMARY -->
                    <div class="row mb-4">
                        <div class="col-lg-6 mb-3">
                            <div class="profile-card">
                                <div class="text-center">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <h4>{{ $user->name }}</h4>
                                    <span class="badge program-badge badge-primary">{{ $user->program }}</span>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="info-item">
                                        <div class="info-icon bg-primary text-white">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Registration Number</div>
                                            <div class="fw-bold">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon bg-success text-white">
                                            <i class="fas fa-calendar"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Academic Year</div>
                                            <div class="fw-bold">{{ $user->year }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon bg-info text-white">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Username</div>
                                            <div class="fw-bold">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-icon bg-warning text-white">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Phone Number</div>
                                            <div class="fw-bold">{{ $user->phone ?? 'Not provided' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- B. RESEARCH STATUS CARD -->
                        <div class="col-lg-6 mb-3">
                            <div class="status-card">
                                <h5 class="mb-3">Research Status</h5>
                                <div class="mb-3">
                                    <span class="badge badge-{{ $project->status_badge_color }} fs-6 p-2">
                                        {{ $project->status_label }}
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Project Title</small>
                                    <h6>{{ $project->title }}</h6>
                                </div>
                                @if ($project->start_date)
                                <div class="mt-2">
                                    <small class="text-muted">Started</small>
                                    <div>{{ $project->start_date->format('M j, Y') }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- C. STATISTICS (IMPORTANT FROM PROPOSAL) -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-number" id="totalMeetings">{{ $stats['totalMeetings'] }}</div>
                                <div class="text-muted">Total Meetings Attended</div>
                                <i class="fas fa-handshake text-primary fs-3 mt-2"></i>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-number" id="totalRevisions">{{ $stats['totalRevisions'] }}</div>
                                <div class="text-muted">Total Proposal Revisions</div>
                                <i class="fas fa-sync-alt text-warning fs-3 mt-2"></i>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-number" id="totalFeedback">{{ $stats['totalFeedback'] }}</div>
                                <div class="text-muted">Total Feedback Received</div>
                                <i class="fas fa-comments text-info fs-3 mt-2"></i>
                            </div>
                        </div>
                    </div>

                    <!-- D. PROPOSAL MANAGEMENT SECTION -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Proposal Management</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadProposalModal">
                                        <i class="fas fa-upload me-2"></i> Upload Proposal
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if ($proposals->count() > 0)
                                        @foreach ($proposals as $proposal)
                                            <div class="proposal-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6>{{ $proposal->title }}</h6>
                                                        <p class="text-muted mb-2">{{ $proposal->abstract ? Str::limit($proposal->abstract, 150) : 'No abstract provided' }}</p>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="badge badge-{{ $proposal->status_badge_color }}">
                                                                {{ $proposal->status_label }}
                                                            </span>
                                                            <span class="badge bg-secondary">{{ $proposal->version_display }}</span>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar"></i> {{ $proposal->submitted_at ? $proposal->submitted_at->format('M j, Y') : 'Not submitted' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        @if ($proposal->file_path)
                                                            <a href="{{ asset('storage/' . $proposal->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No proposals uploaded yet.</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadProposalModal">
                                                <i class="fas fa-upload me-2"></i> Upload Your First Proposal
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- E. FEEDBACK SECTION -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Supervisor Feedback</h5>
                                </div>
                                <div class="card-body">
                                    @if ($feedback->count() > 0)
                                        @foreach ($feedback->take(5) as $item)
                                            <div class="feedback-item">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6>{{ $item->title }}</h6>
                                                    <div class="d-flex gap-2">
                                                        <span class="badge badge-{{ $item->priority_badge_color }}">
                                                            {{ $item->priority_label }}
                                                        </span>
                                                        <span class="badge badge-{{ $item->status_badge_color }}">
                                                            {{ $item->status_label }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="mb-2">{{ $item->comments }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user"></i> {{ $item->supervisor->name }}
                                                        </small>
                                                        <small class="text-muted ms-3">
                                                            <i class="fas fa-calendar"></i> {{ $item->created_at->format('M j, Y') }}
                                                        </small>
                                                        @if ($item->related_entity_name)
                                                            <small class="text-muted ms-3">
                                                                <i class="fas fa-link"></i> {{ $item->related_entity_name }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    @if ($item->status === 'pending')
                                                        <button class="btn btn-sm btn-outline-success" onclick="addressFeedback({{ $item->id }})">
                                                            <i class="fas fa-check"></i> Mark as Addressed
                                                        </button>
                                                    @endif
                                                </div>
                                                @if ($item->student_response)
                                                    <div class="mt-2 p-2 bg-light rounded">
                                                        <small class="text-muted">Your Response:</small>
                                                        <p class="mb-0">{{ $item->student_response }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No feedback received yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- F. MEETING HISTORY (VERY IMPORTANT) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Meeting History</h5>
                                </div>
                                <div class="card-body">
                                    @if ($meetings->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Title</th>
                                                        <th>Status</th>
                                                        <th>Action Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($meetings as $meeting)
                                                        <tr>
                                                            <td>{{ $meeting->formatted_date }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $meeting->type_badge_color }}">
                                                                    {{ $meeting->type_label }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $meeting->title }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $meeting->status_badge_color }}">
                                                                    {{ $meeting->status_label }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($meeting->action_points)
                                                                    <small>{{ Str::limit($meeting->action_points, 50) }}</small>
                                                                @else
                                                                    <small class="text-muted">None</small>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No meetings scheduled yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- G. QUICK ACTIONS -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <button class="quick-action-btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#uploadProposalModal">
                                                <i class="fas fa-upload me-2"></i> Upload Proposal
                                            </button>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <button class="quick-action-btn btn-info w-100" onclick="viewAllFeedback()">
                                                <i class="fas fa-comments me-2"></i> View Feedback
                                            </button>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <button class="quick-action-btn btn-success w-100" onclick="sendMessage()">
                                                <i class="fas fa-envelope me-2"></i> Send Message to Supervisor
                                            </button>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <button class="quick-action-btn btn-warning w-100" onclick="scheduleMeeting()">
                                                <i class="fas fa-calendar-plus me-2"></i> Request Meeting
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Proposal Modal -->
    <div class="modal fade" id="uploadProposalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload New Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadProposalForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="proposalTitle" class="form-label">Proposal Title</label>
                            <input type="text" class="form-control" id="proposalTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="proposalAbstract" class="form-label">Abstract</label>
                            <textarea class="form-control" id="proposalAbstract" name="abstract" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="proposalFile" class="form-label">Proposal File</label>
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="mb-2">Drag and drop your proposal file here or click to browse</p>
                                <p class="text-muted small">Supported formats: PDF, DOC, DOCX (Max: 10MB)</p>
                                <input type="file" id="proposalFile" name="proposal_file" class="d-none" accept=".pdf,.doc,.docx" required>
                            </div>
                            <div id="fileInfo" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label for="submissionNotes" class="form-label">Submission Notes</label>
                            <textarea class="form-control" id="submissionNotes" name="submission_notes" rows="3" placeholder="Any additional notes about this submission..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="uploadProposal()">
                        <i class="fas fa-upload me-2"></i> Upload Proposal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Response Modal -->
    <div class="modal fade" id="feedbackResponseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Address Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="feedbackResponseForm">
                        @csrf
                        <input type="hidden" id="feedbackId" name="feedback_id">
                        <div class="mb-3">
                            <label for="studentResponse" class="form-label">Your Response</label>
                            <textarea class="form-control" id="studentResponse" name="student_response" rows="4" required placeholder="Describe how you have addressed this feedback..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitFeedbackResponse()">
                        <i class="fas fa-check me-2"></i> Submit Response
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upload functionality
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('proposalFile');
        const fileInfo = document.getElementById('fileInfo');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayFileInfo(e.target.files[0]);
            }
        });

        function displayFileInfo(file) {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            fileInfo.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-file me-2"></i>
                    <strong>${file.name}</strong> (${fileSize} MB)
                </div>
            `;
        }

        function uploadProposal() {
            const form = document.getElementById('uploadProposalForm');
            const formData = new FormData(form);
            
            fetch('{{ route("student.upload-proposal") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Proposal uploaded successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the proposal.');
            });
        }

        function addressFeedback(feedbackId) {
            document.getElementById('feedbackId').value = feedbackId;
            const modal = new bootstrap.Modal(document.getElementById('feedbackResponseModal'));
            modal.show();
        }

        function submitFeedbackResponse() {
            const feedbackId = document.getElementById('feedbackId').value;
            const response = document.getElementById('studentResponse').value;
            
            fetch(`{{ route('student.address-feedback', ['feedback' => ':id']) }}`.replace(':id', feedbackId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_response: response
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Feedback marked as addressed!');
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting your response.');
            });
        }

        function viewAllFeedback() {
            // This would typically navigate to a dedicated feedback page
            alert('View all feedback - This would navigate to a comprehensive feedback page');
        }

        function sendMessage() {
            // This would typically open a messaging interface
            alert('Send message to supervisor - This would open a messaging interface');
        }

        function scheduleMeeting() {
            // This would typically open a meeting scheduling interface
            alert('Request meeting - This would open a meeting scheduling interface');
        }

        // Auto-refresh dashboard stats every 30 seconds
        setInterval(() => {
            fetch('{{ route("student.dashboard-stats") }}')
                .then(response => response.json())
                .then(data => {
                    if (!data.error) {
                        document.getElementById('totalMeetings').textContent = data.totalMeetings;
                        document.getElementById('totalRevisions').textContent = data.totalRevisions;
                        document.getElementById('totalFeedback').textContent = data.totalFeedback;
                    }
                })
                .catch(error => console.error('Error updating stats:', error));
        }, 30000);
    </script>
</body>
</html>
