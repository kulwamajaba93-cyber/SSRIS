@extends('layouts.student')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Supervisor Feedback</h1>
        @if($pendingFeedback > 0)
            <span class="badge bg-danger">{{ $pendingFeedback }} Pending</span>
        @endif
    </div>

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
                            @if($item->due_date)
                                <div class="alert @if($item->isOverdue()) alert-danger @else alert-secondary @endif">
                                    <strong>Due Date:</strong> {{ $item->due_date->format('M j, Y') }}
                                    @if($item->isOverdue())
                                        <span class="text-danger">(Overdue)</span>
                                    @endif
                                </div>
                            @endif
                            <small class="text-muted">From: {{ $item->supervisor->name }}</small>
                            <br>
                            <small class="text-muted">Date: {{ $item->created_at->format('M j, Y g:i A') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No feedback received yet</h4>
            <p class="text-muted">Your supervisor will provide feedback here.</p>
        </div>
    @endif
</div>
@endsection
