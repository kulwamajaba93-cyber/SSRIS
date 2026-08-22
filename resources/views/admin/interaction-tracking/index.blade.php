@extends('layouts.admin')
@php
    use App\Models\Meeting;
@endphp

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-dark fw-bold">Interaction Tracking Dashboard</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light text-dark fw-bold">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Student Research Progress Overview</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Assigned Supervisor</th>
                            <th>Current Research Stage</th>
                            <th>Meeting Summary</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php
                                $recordedMeetings = $student->meetingParticipations->where('status', Meeting::STATUS_COMPLETED)->count();

                                // Determine current stage
                                $currentStage = 'Concept Notes';
                                $approvedDocs = $student->proposals->where('status', 'approved')->pluck('document_type')->toArray();
                                if (in_array('report', $approvedDocs)) {
                                    $currentStage = 'Completed';
                                } elseif (in_array('data_collection', $approvedDocs)) {
                                    $currentStage = 'Final Report';
                                } elseif (in_array('proposal', $approvedDocs)) {
                                    $currentStage = 'Data Collection';
                                } elseif (in_array('concept_notes', $approvedDocs)) {
                                    $currentStage = 'Proposal';
                                }

                                // Stage badge color
                                $stageBadgeColor = $currentStage === 'Completed' ? 'success' : 'info';
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $student->name }}</div>
                                    <div class="text-muted small">{{ $student->reg_number }}</div>
                                </td>
                                <td>
                                    @if($student->supervisor)
                                        <div class="fw-bold">{{ $student->supervisor->name }}</div>
                                        @if($student->supervisor->department)
                                            <div class="text-muted small">{{ $student->supervisor->department }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">Not Assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $stageBadgeColor }} px-3 py-2">
                                        <i class="fas fa-flag me-1"></i> {{ $currentStage }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $recordedMeetings }}</strong> Recorded Meetings
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.interaction-tracking.show', $student) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
