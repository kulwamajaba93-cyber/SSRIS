@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Users Management</h1>
                <div>
                    <a href="{{ route('admin.users.import-csv') }}" class="btn btn-success me-2">
                        <i class="fas fa-file-csv"></i> Import CSV
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}">
                        <div class="row">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="search" placeholder="Search by name, programme, or registration number" value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> All Users ({{ $users->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Username/Email</th>
                                        <th>Programme</th>
                                        <th>Phone</th>
                                        <th>Supervisor</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'supervisor' ? 'success' : 'primary') }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->username ?? $user->email }}</td>
                                            <td>{{ $user->program ?? 'N/A' }}</td>
                                            <td>{{ $user->phone ?? 'N/A' }}</td>
                                            <td>{{ $user->supervisor ? $user->supervisor->name : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                    @if($user->id !== auth()->id())
                                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $users->links() }}
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No users found.</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Add User
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modals -->
@foreach($users as $user)
<div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password - {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Reset Type *</label>
                        <select class="form-select" name="reset_type" required onchange="togglePasswordField(this)">
                            <option value="auto">Automatic Reset</option>
                            <option value="manual">Manual Reset</option>
                        </select>
                    </div>
                    <div class="alert alert-info" id="auto-reset-info">
                        @if($user->role === 'student')
                            <strong>Auto-reset:</strong> Password will be regenerated from registration number (mocu.program.number.year)
                        @elseif($user->role === 'supervisor')
                            <strong>Auto-reset:</strong> Password will be set to: password123
                        @else
                            <strong>Auto-reset:</strong> Password will be set to: admin123
                        @endif
                    </div>
                    <div class="mb-3" id="manual-password-field" style="display: none;">
                        <label for="password" class="form-label">New Password *</label>
                        <input type="password" class="form-control" id="password" name="password" minlength="6">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
function togglePasswordField(select) {
    const manualField = document.getElementById('manual-password-field');
    const autoInfo = document.getElementById('auto-reset-info');
    
    if (select.value === 'manual') {
        manualField.style.display = 'block';
        autoInfo.style.display = 'none';
    } else {
        manualField.style.display = 'none';
        autoInfo.style.display = 'block';
    }
}
</script>
@endsection
