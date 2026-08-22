@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Update Research Stage</h1>
                <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Student Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $student->name }}</p>
                    <p><strong>Registration Number:</strong> {{ $student->username }}</p>
                    <p><strong>Program:</strong> {{ $student->program }}</p>
                    <p><strong>Year:</strong> {{ $student->year }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> Update Research Stage</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supervisor.research-stage.update', $student) }}">
                        @method('PUT')
                        @csrf
                        
                        <div class="mb-3">
                            <label for="stage" class="form-label">Current Stage</label>
                            <div class="mb-2">
                                <span class="badge bg-{{ $researchStage->stage_badge_color }} fs-6">
                                    {{ $researchStage->stage_label }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="stage" class="form-label">New Stage *</label>
                            <select class="form-select" id="stage" name="stage" required>
                                <option value="proposal_submitted" {{ $researchStage->stage === 'proposal_submitted' ? 'selected' : '' }}>
                                    Proposal Submitted
                                </option>
                                <option value="under_review" {{ $researchStage->stage === 'under_review' ? 'selected' : '' }}>
                                    Under Review
                                </option>
                                <option value="revision" {{ $researchStage->stage === 'revision' ? 'selected' : '' }}>
                                    Revision Required
                                </option>
                                <option value="approved" {{ $researchStage->stage === 'approved' ? 'selected' : '' }}>
                                    Approved
                                </option>
                                <option value="in_progress" {{ $researchStage->stage === 'in_progress' ? 'selected' : '' }}>
                                    In Progress
                                </option>
                                <option value="completed" {{ $researchStage->stage === 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                            @error('stage')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Add any notes about this stage change">{{ $researchStage->notes ?? '' }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Research Stage Descriptions</h6>
                            <ul class="mb-0">
                                <li><strong>Proposal Submitted:</strong> Student has submitted their initial proposal</li>
                                <li><strong>Under Review:</strong> Proposal is being reviewed by supervisor</li>
                                <li><strong>Revision Required:</strong> Student needs to make revisions to the proposal</li>
                                <li><strong>Approved:</strong> Proposal has been approved</li>
                                <li><strong>In Progress:</strong> Student is working on their research</li>
                                <li><strong>Completed:</strong> Research has been completed</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i> Update Stage
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
