@extends('layouts.admin')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Approve Supervisors</li>
    </ol>
</nav>

<!-- User Info Bar -->
<div class="admin-info-bar">
    <div class="admin-info-item">
        <i class="fas fa-user"></i>
        <span>Logged in as: {{ strtoupper(auth()->user()->name) }}</span>
    </div>
    <div class="admin-info-item">
        <i class="fas fa-calendar"></i>
        <span>Academic Year: 2025/2026</span>
    </div>
    <div class="admin-info-item">
        <i class="fas fa-clock"></i>
        <span>{{ now()->format('l jS \\of F, Y') }}</span>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Approve Supervisors</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-user-check"></i> Pending Supervisor Approvals</h5>
            </div>
            <div class="card-body">
                @if($pendingSupervisors->count() > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ $pendingSupervisors->count() }}</strong> supervisors awaiting approval. Supervisors must be approved before they can supervise research.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" id="selectAll" onchange="toggleAllSupervisors()">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Phone</th>
                                    <th>Registration Date</th>
                                    <th>Completion Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingSupervisors as $supervisor)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="supervisor_ids[]" value="{{ $supervisor->id }}" class="supervisor-checkbox">
                                        </td>
                                        <td>{{ $supervisor->name }}</td>
                                        <td>{{ $supervisor->email }}</td>
                                        <td>{{ $supervisor->department ?? 'N/A' }}</td>
                                        <td>{{ $supervisor->phone ?? 'N/A' }}</td>
                                        <td>{{ $supervisor->created_at->format('M j, Y') }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $supervisor->getCompletionRate() >= 100 ? 'bg-success' : ($supervisor->getCompletionRate() >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                     role="progressbar"
                                                     style="width: {{ $supervisor->getCompletionRate() }}%"
                                                     aria-valuenow="{{ $supervisor->getCompletionRate() }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    {{ number_format($supervisor->getCompletionRate(), 1) }}%
                                                </div>
                                            </div>
                                            @if($supervisor->hasFullCompletion())
                                                <small class="text-success"><i class="fas fa-check-circle"></i> 100% Completed - Recommend Approval</small>
                                            @else
                                                <small class="text-warning"><i class="fas fa-info-circle"></i> {{ number_format($supervisor->getCompletionRate(), 1) }}% Completed - Review Required</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <form method="POST" action="{{ route('admin.supervisors.approve', $supervisor) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.supervisors.reject', $supervisor) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to reject this supervisor? This will delete the supervisor account.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" onclick="approveSelected()">
                            <i class="fas fa-check-double me-2"></i> Approve Selected
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectSelected()">
                            <i class="fas fa-times-circle me-2"></i> Reject Selected
                        </button>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h4>All Supervisors Approved</h4>
                        <p class="text-muted">There are no supervisors awaiting approval at this time.</p>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleAllSupervisors() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.supervisor-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function approveSelected() {
    const checkboxes = document.querySelectorAll('.supervisor-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one supervisor to approve.');
        return;
    }
    
    if (confirm(`Are you sure you want to approve ${checkboxes.length} supervisor(s)?`)) {
        checkboxes.forEach(checkbox => {
            const form = checkbox.closest('tr').querySelector('form[action*="approve"]');
            if (form) {
                form.submit();
            }
        });
    }
}

function rejectSelected() {
    const checkboxes = document.querySelectorAll('.supervisor-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one supervisor to reject.');
        return;
    }
    
    if (confirm(`Are you sure you want to reject ${checkboxes.length} supervisor(s)? This will delete their accounts.`)) {
        checkboxes.forEach(checkbox => {
            const form = checkbox.closest('tr').querySelector('form[action*="reject"]');
            if (form) {
                form.submit();
            }
        });
    }
}
</script>
@endsection
