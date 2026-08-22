@extends('layouts.admin')

@section('content')
<!-- Breadcrumbs -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Interaction Tracking</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Interaction Tracking</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-users"></i> All Students</h5>
            </div>
            <div class="card-body">
                @if($students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Registration Number</th>
                                    <th>Supervisor</th>
                                    <th>Last Interaction</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->username }}</td>
                                        <td>{{ $student->supervisor ? $student->supervisor->name : 'Not Assigned' }}</td>
                                        <td>
                                            @php
                                                $lastInteraction = $student->interactionsAsStudent()->latest()->first();
                                            @endphp
                                            {{ $lastInteraction ? $lastInteraction->created_at->diffForHumans() : 'No interactions' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.interactions.show', $student) }}" class="btn btn-sm btn-primary">View Timeline</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No students yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
