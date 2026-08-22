@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Give Feedback</h1>
                <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> {{ $proposal->document_type_label }}: {{ $proposal->title }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Student:</strong> {{ $proposal->student->name }}</p>
                    <p><strong>Version:</strong> {{ $proposal->version_display }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $proposal->status_badge_color }}">{{ $proposal->status_label }}</span></p>
                    <p><strong>Abstract:</strong> {{ Str::limit($proposal->abstract, 200) }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-comment"></i> Submit Feedback</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supervisor.feedback.store', $proposal) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Feedback Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required autofocus>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comments" class="form-label">Comments *</label>
                            <textarea class="form-control" id="comments" name="comments" rows="5" required></textarea>
                            @error('comments')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority *</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('priority')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="action_required" class="form-label">Action Required</label>
                            <textarea class="form-control" id="action_required" name="action_required" rows="2" placeholder="Specific actions the student needs to take"></textarea>
                            @error('action_required')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date">
                            @error('due_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="proposal_status" class="form-label">{{ $proposal->document_type_label }} Status</label>
                            <select class="form-select" id="proposal_status" name="proposal_status">
                                <option value="">-- Keep Current Status --</option>
                                <option value="pending">Pending</option>
                                <option value="under_review">Under Review</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="revision">Revision Required</option>
                            </select>
                            @error('proposal_status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> High and urgent priority will automatically set the {{ strtolower($proposal->document_type_label) }} status to "Revision Required". You can also manually set the {{ strtolower($proposal->document_type_label) }} status here.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane me-2"></i> Submit Feedback & Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
