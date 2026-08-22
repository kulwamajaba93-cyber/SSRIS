@extends('layouts.admin')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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

    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalUsers }}</h3>
                    <p class="mb-0 opacity-75">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalStudents }}</h3>
                    <p class="mb-0 opacity-75">Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalSupervisors }}</h3>
                    <p class="mb-0 opacity-75">Supervisors</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card h-100 bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-user-shield fa-3x mb-2 opacity-75"></i>
                    <h3 class="mb-0">{{ $totalAdmins }}</h3>
                    <p class="mb-0 opacity-75">Admins</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i> Add User
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.assign-supervisor') }}" class="btn btn-info w-100">
                                <i class="fas fa-user-tie me-2"></i> Assign Supervisor
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-cog me-2"></i> Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#supervisorPerformanceModal">
                                <i class="fas fa-check-circle me-2"></i> Approve Supervisor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervisor Performance Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Supervisor Performance</h5>
                </div>
                <div class="card-body">
                    @if(count($supervisorPerformance) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Supervisor Name</th>
                                        <th>Assigned Students</th>
                                        <th>Total Concept Notes</th>
                                        <th>Approved Concept Notes</th>
                                        <th>Total Proposals</th>
                                        <th>Approved Proposals</th>
                                        <th>Total Data Collection</th>
                                        <th>Approved Data Collection</th>
                                        <th>Total Reports</th>
                                        <th>Approved Reports</th>
                                        <th>Completed Students</th>
                                        <th>Completion Rate (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supervisorPerformance as $perf)
                                        <tr>
                                            <td>{{ $perf['supervisor']->name }}</td>
                                            <td>{{ $perf['assigned_students_count'] }}</td>
                                            <td>{{ $perf['total_concept_notes'] ?? 0 }}</td>
                                            <td>{{ $perf['approved_concept_notes'] ?? 0 }}</td>
                                            <td>{{ $perf['total_proposals'] }}</td>
                                            <td>{{ $perf['approved_proposals'] }}</td>
                                            <td>{{ $perf['total_data_collection'] ?? 0 }}</td>
                                            <td>{{ $perf['approved_data_collection'] ?? 0 }}</td>
                                            <td>{{ $perf['total_reports'] ?? 0 }}</td>
                                            <td>{{ $perf['approved_reports'] ?? 0 }}</td>
                                            <td>{{ $perf['completed_students'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $perf['completion_rate'] >= 70 ? 'success' : ($perf['completion_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                    {{ $perf['completion_rate'] }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No supervisor data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Supervisor Modal - Supervisor approval only -->
    <div class="modal fade" id="supervisorPerformanceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle"></i> Approve Supervisor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(count($supervisorPerformance) > 0)
                        <p class="text-muted mb-3">Approve only when completion rate is 100%. Reject is available at any time.</p>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Supervisor Name</th>
                                        <th>Completion Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supervisorPerformance as $perf)
                                        <tr>
                                            <td>{{ $perf['supervisor']->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $perf['completion_rate'] >= 100 ? 'success' : 'secondary' }}">
                                                    {{ $perf['completion_rate'] }}%
                                                </span>
                                            </td>
                                            <td>
                                                @if($perf['supervisor']->isPerformanceApproved())
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle"></i> Approved
                                                    </span>
                                                    @if($perf['supervisor']->performance_signed_at)
                                                        <small class="text-muted d-block">{{ $perf['supervisor']->performance_signed_at->format('M j, Y') }}</small>
                                                    @endif
                                                    @if($perf['supervisor']->performance_hod_remarks)
                                                        <small class="text-info d-block">{{ $perf['supervisor']->performance_hod_remarks }}</small>
                                                    @endif
                                                @elseif($perf['supervisor']->isPerformanceRejected())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times-circle"></i> Rejected
                                                    </span>
                                                    @if($perf['supervisor']->performance_hod_remarks)
                                                        <small class="text-danger d-block">{{ $perf['supervisor']->performance_hod_remarks }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    @if(!$perf['supervisor']->isPerformanceApproved())
                                                        @if($perf['completion_rate'] >= 100)
                                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $perf['supervisor']->id }}">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-success" disabled title="Approval requires 100% completion rate">
                                                                <i class="fas fa-check"></i> Approve
                                                            </button>
                                                        @endif
                                                    @endif

                                                    @if(!$perf['supervisor']->isPerformanceRejected())
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $perf['supervisor']->id }}">
                                                            <i class="fas fa-times"></i> Reject
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No supervisor data available</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve/Reject Modals for each supervisor -->
    @foreach($supervisorPerformance as $perf)
        @if(!$perf['supervisor']->isPerformanceApproved())
            <!-- Approve Modal -->
            <div class="modal fade" id="approveModal{{ $perf['supervisor']->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-check-circle"></i> Approve Supervisor Performance
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to approve the performance of <strong>{{ $perf['supervisor']->name }}</strong>?</p>
                            <div class="alert alert-info">
                                <strong>Completion Rate:</strong> {{ $perf['completion_rate'] }}%
                            </div>
                            <div class="mb-3">
                                <label for="remarks{{ $perf['supervisor']->id }}" class="form-label">Remarks (Optional)</label>
                                <textarea class="form-control" id="remarks{{ $perf['supervisor']->id }}" name="remarks" rows="3" maxlength="500" placeholder="Add any comments about this approval..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-success" onclick="approvePerformance({{ $perf['supervisor']->id }}, document.getElementById('remarks{{ $perf['supervisor']->id }}').value)">
                                <i class="fas fa-check"></i> Approve Performance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reject Modal -->
        @if(!$perf['supervisor']->isPerformanceRejected())
            <div class="modal fade" id="rejectModal{{ $perf['supervisor']->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-times-circle"></i>
                                Reject Supervisor Performance
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Are you sure you want to reject the performance of <strong>{{ $perf['supervisor']->name }}</strong>?
                            </p>
                            <div class="alert alert-warning">
                                <strong>Completion Rate:</strong> {{ $perf['completion_rate'] }}%
                            </div>
                            <div class="mb-3">
                                <label for="rejectRemarks{{ $perf['supervisor']->id }}" class="form-label">Remarks <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="rejectRemarks{{ $perf['supervisor']->id }}" name="remarks" rows="3" maxlength="500" required placeholder="Please provide a reason for rejection..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" onclick="rejectPerformance({{ $perf['supervisor']->id }}, document.getElementById('rejectRemarks{{ $perf['supervisor']->id }}').value)">
                                <i class="fas fa-times"></i> Reject Performance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        function performanceRequest(url, supervisorId, remarks = null) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                alert('Session expired. Please refresh the page and log in again.');
                return;
            }

            const body = { supervisor_id: supervisorId };
            if (remarks !== null && remarks.trim() !== '') {
                body.remarks = remarks.trim();
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            })
            .then(async (response) => {
                const data = await response.json().catch(() => null);

                if (!response.ok) {
                    const message = data?.message
                        || (data?.errors ? Object.values(data.errors).flat().join(' ') : null)
                        || 'Request failed. Please refresh the page and try again.';
                    throw new Error(message);
                }

                return data;
            })
            .then((data) => {
                if (data.success) {
                    alert(data.message);
                    // Reload the page to update the status
                    location.reload();
                } else {
                    throw new Error(data.message || 'Unknown error');
                }
            })
            .catch((error) => {
                alert('Error: ' + error.message);
            });
        }

        function approvePerformance(supervisorId, remarks) {
            if (!confirm('Are you sure you want to approve this supervisor performance?')) {
                return;
            }

            performanceRequest(
                '{{ route('admin.supervisors.performance.approve') }}',
                supervisorId,
                remarks
            );
        }

        function rejectPerformance(supervisorId, remarks) {
            if (!remarks || remarks.trim() === '') {
                alert('Please provide a reason for rejection.');
                return;
            }

            if (!confirm('Are you sure you want to reject this supervisor performance?')) {
                return;
            }

            performanceRequest(
                '{{ route('admin.supervisors.performance.reject') }}',
                supervisorId,
                remarks
            );
        }
    </script>

    <!-- Unassigned Students Alert -->
    @if($unassignedStudents->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i>Unassigned Students</h5>
                    <p class="card-text">{{ $unassignedStudents->count() }} students without supervisors.</p>
                    <a href="{{ route('admin.assign-supervisor') }}" class="btn btn-primary btn-sm">Assign Supervisors</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reports & Analytics Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Reports & Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Students by Program -->
                        <div class="col-md-4 mb-4">
                            <h6>Students by Program</h6>
                            @if($studentsByProgram->count() > 0)
                                <ul class="list-group">
                                    @foreach($studentsByProgram as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $item->program }}
                                            <span class="badge bg-primary rounded-pill">{{ $item->count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No data available</p>
                            @endif
                        </div>

                        <!-- Students by Year -->
                        <div class="col-md-4 mb-4">
                            <h6>Students by Year</h6>
                            @if($studentsByYear->count() > 0)
                                <ul class="list-group">
                                    @foreach($studentsByYear as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Year {{ $item->year }}
                                            <span class="badge bg-success rounded-pill">{{ $item->count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No data available</p>
                            @endif
                        </div>

                        <!-- Document Submissions -->
                        <div class="col-md-4 mb-4">
                            <h6>Document Submissions</h6>
                            @if($studentsByStage->count() > 0)
                                <ul class="list-group">
                                    @foreach($studentsByStage as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            @php
                                                $stageLabel = match($item->document_type) {
                                                    'proposal' => 'Proposals Submitted',
                                                    'data_collection' => 'Data Collection & Analysis',
                                                    'report' => 'Reports Submitted',
                                                    'concept_notes' => 'Concept Notes Submitted',
                                                    default => 'Unknown'
                                                };
                                            @endphp
                                            {{ $stageLabel }}
                                            <span class="badge bg-info rounded-pill">{{ $item->count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Recent Users</h5>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Program</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td><span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'supervisor' ? 'success' : 'primary') }}">{{ ucfirst($user->role) }}</span></td>
                                            <td>{{ $user->program ?? 'N/A' }}</td>
                                            <td>{{ $user->created_at->format('M j, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No users registered yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
