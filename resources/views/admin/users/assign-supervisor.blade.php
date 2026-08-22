@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Assign Supervisors to Students</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie"></i> Assign Supervisor</h5>
                </div>
                <div class="card-body">
                    @if($students->count() > 0)
                        <form method="POST" action="{{ route('admin.assign-supervisor.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="supervisor_id" class="form-label">Select Supervisor *</label>
                                <select class="form-select" id="supervisor_id" name="supervisor_id" required>
                                    <option value="">Choose a Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}">
                                            {{ $supervisor->name }} ({{ $supervisor->email }})
                                            @if($supervisor->department)
                                                - {{ $supervisor->department }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Students *</label>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">
                                                    <input type="checkbox" id="selectAll" onchange="toggleAllStudents()">
                                                </th>
                                                <th>Name</th>
                                                <th>Registration Number</th>
                                                <th>Program</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($students as $student)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                                                    </td>
                                                    <td>{{ $student->name }}</td>
                                                    <td>{{ $student->username }}</td>
                                                    <td>{{ $student->program }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @error('student_ids')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ $students->count() }}</strong> unassigned students available for assignment.
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i> Assign Selected
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No unassigned students found.</p>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAllStudents() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}
</script>
@endsection
