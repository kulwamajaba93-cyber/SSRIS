@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Bulk Create Students</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Add Multiple Students</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.bulk-store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="supervisor_id" class="form-label">Assign Supervisor (Optional)</label>
                            <select class="form-select" id="supervisor_id" name="supervisor_id">
                                <option value="">No Supervisor</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }} ({{ $supervisor->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Instructions</h6>
                            <ul class="mb-0">
                                <li>Click "Add Student" to add more student rows</li>
                                <li>Programme must be: <strong>BBICT</strong>, <strong>BHRM</strong>, or <strong>BAT</strong></li>
                                <li>Registration Number: Enter only the number part (e.g., 1089)</li>
                                <li>Phone Number: Required for SMS notifications</li>
                                <li>Passwords will be auto-generated using format: <code>mocu.program.number.year</code></li>
                            </ul>
                        </div>

                        <div id="students-container">
                            <!-- Student rows will be added here -->
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" onclick="addStudentRow()">
                                <i class="fas fa-plus me-2"></i> Add Student
                            </button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i> Create Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let studentCount = 0;

function addStudentRow() {
    studentCount++;
    const container = document.getElementById('students-container');
    
    const row = document.createElement('div');
    row.className = 'card mb-3 student-row';
    row.id = `student-${studentCount}`;
    row.innerHTML = `
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Student #${studentCount}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeStudentRow(${studentCount})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" class="form-control" name="students[${studentCount}][name]" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Programme *</label>
                    <select class="form-select" name="students[${studentCount}][program]" required>
                        <option value="">Select Programme</option>
                        <option value="BBICT">BBICT</option>
                        <option value="BHRM">BHRM</option>
                        <option value="BAT">BAT</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="students[${studentCount}][phone]" placeholder="0699889430">
                    <small class="text-muted">Required for SMS notifications</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Registration Number *</label>
                    <input type="text" class="form-control" name="students[${studentCount}][reg_number]" placeholder="1089" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Year *</label>
                    <input type="text" class="form-control" name="students[${studentCount}][year]" placeholder="23" required>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(row);
}

function removeStudentRow(id) {
    const row = document.getElementById(`student-${id}`);
    if (row) {
        row.remove();
    }
}

// Add one student row by default
document.addEventListener('DOMContentLoaded', function() {
    addStudentRow();
});
</script>
@endsection
