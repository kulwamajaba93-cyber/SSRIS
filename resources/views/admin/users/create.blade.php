@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Create New User</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> User Registration</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}" id="userForm">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="student">Student</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Student Fields -->
                        <div id="student-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="registration_number" class="form-label">Registration Number *</label>
                                <input type="text" class="form-control" id="registration_number" name="registration_number" placeholder="MOCU/BBICT/1089/23">
                                <small class="text-muted">Format: MOCU/PROGRAM/NUMBER/YEAR (e.g., MOCU/BBICT/1089/23)</small>
                                @error('registration_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="student_phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="student_phone" name="phone" placeholder="0699889430">
                                <small class="text-muted">Required for SMS notifications</small>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="supervisor_id" class="form-label">Assign Supervisor</label>
                                <select class="form-select" id="supervisor_id" name="supervisor_id">
                                    <option value="">No Supervisor</option>
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}">{{ $supervisor->name }} ({{ $supervisor->email }})</option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Password will be auto-generated using format: <code>mocu.program.number.year</code>
                            </div>
                        </div>

                        <!-- Supervisor/Admin Fields -->
                        <div id="supervisor-fields" style="display: none;">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="supervisor_phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="supervisor_phone" name="phone" placeholder="0699889430">
                                <small class="text-muted">Required for SMS notifications</small>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" placeholder="e.g., Computer Science">
                                @error('department')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const studentFields = document.getElementById('student-fields');
    const supervisorFields = document.getElementById('supervisor-fields');
    const form = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');

    function toggleFields() {
        const role = roleSelect.value;
        const studentInputs = studentFields.querySelectorAll('input, select');
        const supervisorInputs = supervisorFields.querySelectorAll('input, select');

        if (role === 'student') {
            studentFields.style.display = 'block';
            supervisorFields.style.display = 'none';
            // Enable student inputs, disable supervisor inputs
            studentInputs.forEach(input => input.disabled = false);
            supervisorInputs.forEach(input => input.disabled = true);
        } else if (role === 'supervisor' || role === 'admin') {
            studentFields.style.display = 'none';
            supervisorFields.style.display = 'block';
            // Enable supervisor inputs, disable student inputs
            studentInputs.forEach(input => input.disabled = true);
            supervisorInputs.forEach(input => input.disabled = false);
        } else {
            studentFields.style.display = 'none';
            supervisorFields.style.display = 'none';
            studentInputs.forEach(input => input.disabled = true);
            supervisorInputs.forEach(input => input.disabled = true);
        }
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields(); // Call on load

    form.addEventListener('submit', function(e) {
        console.log('Form submitting...');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';
    });
});
</script>
@endsection
