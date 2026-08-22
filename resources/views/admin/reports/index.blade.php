@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Reports & Analytics</h1>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Overall System Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Overall System Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $totalStudents }}</h3>
                                <p class="mb-0">Total Students</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h3 class="text-success">{{ $totalSupervisors }}</h3>
                                <p class="mb-0">Total Supervisors</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <h3 class="text-warning">{{ $totalProposals }}</h3>
                                <p class="mb-0">Total Proposals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Document Submissions Distribution -->
    <div class="row mb-4">
        @if($studentsByStage->count() > 0)
            @foreach($studentsByStage as $item)
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="card h-100">
                        @php
                            $stageLabel = match($item->document_type) {
                                'proposal' => 'Proposals Submitted',
                                'data_collection' => 'Data Collection & Analysis',
                                'report' => 'Reports Submitted',
                                'concept_notes' => 'Concept Notes Submitted',
                                default => 'Unknown'
                            };
                            $color = match($item->document_type) {
                                'proposal' => 'primary',
                                'data_collection' => 'success',
                                'report' => 'info',
                                'concept_notes' => 'warning',
                                default => 'secondary'
                            };
                            $icon = match($item->document_type) {
                                'proposal' => 'file-alt',
                                'data_collection' => 'database',
                                'report' => 'book',
                                'concept_notes' => 'lightbulb',
                                default => 'file'
                            };
                        @endphp
                        <div class="card-body text-center bg-{{ $color }} text-white">
                            <i class="fas fa-{{ $icon }} fa-3x mb-2 opacity-75"></i>
                            <h3 class="mb-0">{{ $item->count }}</h3>
                            <p class="mb-0 opacity-75">{{ $stageLabel }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-muted">No document submission data available</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Supervisor Performance Report -->
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
                                        <th>Total Proposals</th>
                                        <th>Approved Proposals</th>
                                        <th>Total Data Collection</th>
                                        <th>Approved Data Collection</th>
                                        <th>Total Reports</th>
                                        <th>Approved Reports</th>
                                        <th>Total Feedback</th>
                                        <th>Completed Students</th>
                                        <th>Completion Rate</th>
                                        <th>HOD Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supervisorPerformance as $perf)
                                        <tr>
                                            <td>{{ $perf['supervisor']->name }}</td>
                                            <td>{{ $perf['assigned_students_count'] }}</td>
                                            <td>{{ $perf['total_proposals'] }}</td>
                                            <td>{{ $perf['approved_proposals'] }}</td>
                                            <td>{{ $perf['total_data_collection'] ?? 0 }}</td>
                                            <td>{{ $perf['approved_data_collection'] ?? 0 }}</td>
                                            <td>{{ $perf['total_reports'] ?? 0 }}</td>
                                            <td>{{ $perf['approved_reports'] ?? 0 }}</td>
                                            <td>{{ $perf['total_feedback'] }}</td>
                                            <td>{{ $perf['completed_students'] }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $perf['completion_rate'] >= 70 ? 'success' : ($perf['completion_rate'] >= 50 ? 'warning' : 'danger') }}"
                                                         role="progressbar"
                                                         style="width: {{ $perf['completion_rate'] }}%">
                                                        {{ $perf['completion_rate'] }}%
                                                    </div>
                                                </div>
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
                                                    @if(auth()->user()->isAdmin() || auth()->user()->isHod())
                                                        <div class="btn-group">
                                                            @if($perf['completion_rate'] >= 100)
                                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $perf['supervisor']->id }}">
                                                                    <i class="fas fa-check"></i> Approve
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-sm btn-success" disabled title="Requires 100% completion rate">
                                                                    <i class="fas fa-check"></i> Approve
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $perf['supervisor']->id }}">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                @endif
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

    <!-- Approve/Reject Modals for each supervisor -->
    @foreach($supervisorPerformance as $perf)
        <!-- Approve Modal (only for 100% completion) -->
        @if($perf['supervisor']->isPerformancePending() && $perf['completion_rate'] >= 100)
            <div class="modal fade" id="approveModal{{ $perf['supervisor']->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-check-circle"></i> Approve Supervisor Performance
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.supervisors.performance.approve') }}">
                            @csrf
                            <input type="hidden" name="supervisor_id" value="{{ $perf['supervisor']->id }}">
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
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve Performance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Reject Modal (for any pending supervisor) -->
        @if($perf['supervisor']->isPerformancePending())
            <div class="modal fade" id="rejectModal{{ $perf['supervisor']->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-times-circle"></i> Reject Supervisor Performance
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.supervisors.performance.reject') }}">
                            @csrf
                            <input type="hidden" name="supervisor_id" value="{{ $perf['supervisor']->id }}">
                            <div class="modal-body">
                                <p>Are you sure you want to reject the performance of <strong>{{ $perf['supervisor']->name }}</strong>?</p>
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
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Reject Performance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection
