@extends('layouts.student')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Feedback Details</h1>
        <a href="{{ route('student.feedback.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Feedback
        </a>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4 mb-md-0">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-comment me-2"></i>{{ $feedback->title ?? 'Feedback' }}
                    </h5>
                    <span class="badge bg-white text-dark">{{ $feedback->priority_label }} Priority</span>
                </div>
                <div class="card-body">
                    @if($feedback->status === 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-bell me-2"></i><strong>New Feedback - Please review and take action</strong>
                        </div>
                    @endif

                    <div class="mb-3">
                        <span class="badge bg-{{ $feedback->priority_badge_color }} me-2">{{ $feedback->priority_label }}</span>
                        <span class="badge bg-{{ $feedback->status_badge_color }}">{{ $feedback->status_label }}</span>
                    </div>

                    @if($feedback->proposal)
                        <div class="mb-3">
                            <small class="text-muted">For:</small>
                            <span class="badge ms-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                {{ $feedback->proposal->document_type_label }}
                            </span>
                        </div>
                    @endif
                    
                    <h6 class="mb-2">Comments</h6>
                    <p class="mb-3">{{ $feedback->comments }}</p>
                    
                    @if($feedback->action_required)
                        <div class="alert alert-info">
                            <h6 class="mb-1"><i class="fas fa-tasks me-2"></i>Action Required</h6>
                            <p class="mb-0">{{ $feedback->action_required }}</p>
                        </div>
                    @endif

                    @if($feedback->due_date)
                        <div class="alert @if($feedback->isOverdue()) alert-danger @else alert-info @endif">
                            <h6 class="mb-1"><i class="fas fa-calendar me-2"></i>Due Date</h6>
                            <p class="mb-0">{{ $feedback->due_date->format('M j, Y') }}</p>
                            @if($feedback->isOverdue())
                                <strong class="text-danger mt-1 d-block">This feedback is overdue!</strong>
                            @endif
                        </div>
                    @endif

                    @if($feedback->student_response)
                        <div class="alert alert-success">
                            <h6 class="mb-1"><i class="fas fa-reply me-2"></i>Your Response</h6>
                            <p class="mb-0">{{ $feedback->student_response }}</p>
                        </div>
                    @endif

                    @if($feedback->addressed_date)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><strong>Addressed on:</strong> {{ $feedback->addressed_date->format('M j, Y g:i A') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Feedback Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="ps-0"><strong>From:</strong></td>
                            <td class="pe-0">{{ $feedback->supervisor->name }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Date:</strong></td>
                            <td class="pe-0">{{ $feedback->created_at->format('M j, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Priority:</strong></td>
                            <td class="pe-0"><span class="badge bg-{{ $feedback->priority_badge_color }}">{{ $feedback->priority_label }}</span></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Status:</strong></td>
                            <td class="pe-0"><span class="badge bg-{{ $feedback->status_badge_color }}">{{ $feedback->status_label }}</span></td>
                        </tr>
                        @if($feedback->addressed_date)
                        <tr>
                            <td class="ps-0"><strong>Addressed:</strong></td>
                            <td class="pe-0">{{ $feedback->addressed_date->format('M j, Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($feedback->status === 'pending')
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Respond to Feedback</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.feedback.address', $feedback) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="student_response" class="form-label">Your Response</label>
                            <textarea class="form-control" id="student_response" name="student_response" rows="4" required placeholder="Describe how you have addressed this feedback..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-2"></i>Submit Response
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
