@extends('layouts.supervisor')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('supervisor.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Students</li>
    </ol>
</nav>

<!-- User Info Bar -->
<div class="supervisor-info-bar">
    <div class="supervisor-info-item">
        <i class="fas fa-user"></i>
        <span>Logged in as: {{ strtoupper(auth()->user()->name) }}</span>
    </div>
    <div class="supervisor-info-item">
        <i class="fas fa-calendar"></i>
        <span>Academic Year: 2025/2026</span>
    </div>
    <div class="supervisor-info-item">
        <i class="fas fa-clock"></i>
        <span>{{ now()->format('l jS \\of F, Y') }}</span>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Assigned Students</h1>
            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users"></i> My Students</h5>
            </div>
            <div class="card-body">
                @if($assignedStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Year</th>
                                    <th>Reg Number</th>
                                    <th>Proposal Status</th>
                                    <th>Data Collection & Analysis</th>
                                    <th>Report Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignedStudents as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->program }}</td>
                                        <td>{{ $student->year }}</td>
                                        <td>{{ $student->username }}</td>
                                        <td>
                                            @php $hasProposal = $student->proposals->where('document_type', 'proposal')->count() > 0; @endphp
                                            @if($hasProposal)
                                                <span class="badge bg-success">Proposal Submitted</span>
                                            @else
                                                <span class="badge bg-secondary">No Proposal Submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $hasDataCollection = $student->proposals->where('document_type', 'data_collection')->count() > 0; @endphp
                                            @if($hasDataCollection)
                                                <span class="badge bg-success">Submitted</span>
                                            @else
                                                <span class="badge bg-secondary">Not Submitted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $hasReport = $student->proposals->where('document_type', 'report')->count() > 0; @endphp
                                            @if($hasReport)
                                                <span class="badge bg-success">Submitted</span>
                                            @else
                                                <span class="badge bg-secondary">Not Submitted</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No students assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
