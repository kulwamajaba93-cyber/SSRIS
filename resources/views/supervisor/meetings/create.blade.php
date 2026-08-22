@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Record Meeting</h1>
                <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> New Meeting Record</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supervisor.meetings.store') }}">
                        @csrf
                        <input type="hidden" name="action_type" value="create" id="action_type">
                        
                        <div class="mb-3">
                            <label class="form-label">Students *</label>
                            <div class="card p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($assignedStudents as $student)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student_{{ $student->id }}">
                                        <label class="form-check-label" for="student_{{ $student->id }}">
                                            {{ $student->name }} ({{ $student->username }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('student_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Meeting Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meeting_date" class="form-label">Meeting Date & Time *</label>
                            <input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date" required>
                            @error('meeting_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="meeting_url" class="form-label">Google Meet URL *</label>
                            <input type="text" class="form-control" id="meeting_url" name="meeting_url" placeholder="meet.google.com/qut-svvo-msq" required>
                            @error('meeting_url')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i> Schedule Meeting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
