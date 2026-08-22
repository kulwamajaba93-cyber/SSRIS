@extends('layouts.admin')
@php
    use App\Models\Meeting;
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 text-dark fw-bold">Student Research Details</h2>
            <p class="text-muted mb-0">{{ $student->name }} ({{ $student->reg_number }})</p>
        </div>
        <a href="{{ route('admin.interaction-tracking.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Overview
        </a>
    </div>

    <!-- Current Research Stage -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light text-dark fw-bold">
            <h5 class="mb-0"><i class="fas fa-route"></i> Current Research Stage</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <span class="badge bg-{{ $currentStage === 'Completed' ? 'success' : 'info' }} fs-6 px-4 py-2">
                    <i class="fas fa-flag-checkered me-2"></i> {{ $currentStage }}
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Document Status Breakdown -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light text-dark fw-bold">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Document Status Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $docLabels = [
                                'concept_notes' => 'Concept Note',
                                'proposal' => 'Proposal',
                                'data_collection' => 'Data Collection',
                                'report' => 'Final Report'
                            ];
                        @endphp
                        @foreach($documentTypes as $docType)
                            @php
                                $doc = $student->proposals->where('document_type', $docType)->first();
                            @endphp
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                                    <span class="fw-medium text-dark">{{ $docLabels[$docType] }}</span>
                                    @if($doc)
                                        <span class="badge bg-{{ $doc->status_badge_color }}">
                                            {{ $doc->status_label }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not Started</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor Info & Meeting Summary -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light text-dark fw-bold">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Meeting Summary</h5>
                </div>
                <div class="card-body">
                    <div class="text-center p-3 border rounded mb-3">
                        @php
                            $recordedMeetings = $student->meetingParticipations->where('status', Meeting::STATUS_COMPLETED)->count();
                        @endphp
                        <h3 class="mb-0">{{ $recordedMeetings }}</h3>
                        <p class="text-muted mb-0">Recorded Meetings</p>
                    </div>

                    @if($student->supervisor)
                        <hr>
                        <h6 class="fw-bold text-dark">Supervisor</h6>
                        <p class="mb-0">{{ $student->supervisor->name }}</p>
                        @if($student->supervisor->department)
                            <small class="text-muted">{{ $student->supervisor->department }}</small>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting History -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light text-dark fw-bold">
            <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Meeting History</h5>
        </div>
        <div class="card-body p-0">
            @php
                $recordedMeetingParticipations = $student->meetingParticipations->where('status', Meeting::STATUS_COMPLETED)->sortByDesc('meeting_date');
            @endphp
            @if($recordedMeetingParticipations->isEmpty())
                <p class="p-4 text-center text-muted">No recorded meetings yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-dark fw-bold">Date</th>
                                <th class="text-dark fw-bold">Topic</th>
                                <th class="text-dark fw-bold">Discussion Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recordedMeetingParticipations as $meeting)
                                <tr>
                                    <td>{{ $meeting->meeting_date->format('F j, Y H:i') }}</td>
                                    <td>{{ $meeting->title }}</td>
                                    <td class="text-muted small">
                                        {{ $meeting->discussion_notes ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
