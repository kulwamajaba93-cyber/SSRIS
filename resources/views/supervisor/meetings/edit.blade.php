@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Edit Meeting</h1>
                <a href="{{ route('supervisor.meetings.show', $meeting) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Meeting
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Meeting Record</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supervisor.meetings.update', $meeting) }}">
                        @method('PUT')
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Students *</label>
                            <select class="form-select" name="student_ids[]" multiple required>
                                @foreach($assignedStudents as $student)
                                    <option value="{{ $student->id }}" 
                                        {{ (
                                            $meeting->students->contains('id', $student->id) || 
                                            $meeting->student_id === $student->id
                                        ) ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->username }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Meeting Title *</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $meeting->title ?? '' }}" required>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="meeting_date" class="form-label">Meeting Date & Time *</label>
                            <input type="datetime-local" class="form-control" id="meeting_date" name="meeting_date" 
                                   value="{{ $meeting->meeting_date ? $meeting->meeting_date->format('Y-m-d\TH:i') : '' }}" required>
                            @error('meeting_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="meeting_url" class="form-label">Google Meet URL *</label>
                            <input type="text" class="form-control" id="meeting_url" name="meeting_url" value="{{ $meeting->meeting_url ?? '' }}" placeholder="meet.google.com/qut-svvo-msq" required>
                            @error('meeting_url')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="scheduled" {{ $meeting->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="completed" {{ $meeting->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $meeting->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discussion_notes" class="form-label">Discussion Notes</label>
                            <textarea class="form-control" id="discussion_notes" name="discussion_notes" rows="4">{{ $meeting->discussion_notes ?? '' }}</textarea>
                            @error('discussion_notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="action_points" class="form-label">Action Points</label>
                            <textarea class="form-control" id="action_points" name="action_points" rows="3">{{ $meeting->action_points ?? '' }}</textarea>
                            @error('action_points')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('supervisor.meetings.show', $meeting) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i> Update Meeting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
