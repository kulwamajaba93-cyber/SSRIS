@extends('layouts.admin')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.interactions.index') }}">Interaction Tracking</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $student->name }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Interaction Timeline - {{ $student->name }}</h1>
            <a href="{{ route('admin.interactions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Timeline</h5>
            </div>
            <div class="card-body">
                @if($interactions->count() > 0)
                    <div class="timeline">
                        @foreach($interactions as $interaction)
                            <div class="timeline-item mb-4">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $interaction->action_type_label }}</strong>
                                            @if($interaction->document_reference)
                                                <span class="badge bg-secondary ms-2">{{ $interaction->document_reference }}</span>
                                            @endif
                                        </div>
                                        <span class="badge bg-{{ $interaction->status_badge_color }}">{{ $interaction->status_label }}</span>
                                    </div>
                                    <div class="card-body">
                                        @if($interaction->notes)
                                            <p class="mb-2">{{ $interaction->notes }}</p>
                                        @endif
                                        <small class="text-muted">
                                            {{ $interaction->created_at->format('M j, Y g:i A') }}
                                            @if($interaction->turnaround_days !== null)
                                                <span class="ms-2">
                                                    Turnaround: {{ $interaction->turnaround_days }} days
                                                    @if($interaction->isOverdue())
                                                        <span class="text-danger ms-1"><i class="fas fa-exclamation-triangle"></i> Over 7 days</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No interactions for this student yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
}
.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #0d6efd;
    border: 2px solid #fff;
}
</style>
@endsection
