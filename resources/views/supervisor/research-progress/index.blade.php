@extends('layouts.supervisor')

@section('title', 'Student Research Progress')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Student Research Progress</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($students->isEmpty())
            <div class="alert alert-info">
                No students assigned yet.
            </div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Registration Number</th>
                                    <th>Current Stage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                    <tr>
                                        <td>{{ $student->name }}</td>
                                        <td>{{ $student->username }}</td>
                                        <td>
                                            @php
                            $studentProgress = $student->studentProgress->sortBy('stage.step_number');
                            $currentStage = $studentProgress->firstWhere('status', 'pending');
                            $lastApproved = $studentProgress->filter(function ($item) {
                                return $item->status === 'approved';
                            })->last();
                        @endphp
                                            @if($currentStage)
                                                <span class="badge bg-warning text-dark">{{ $currentStage->stage->name }}</span>
                                            @elseif($lastApproved)
                                                <span class="badge bg-success">{{ $lastApproved->stage->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Not Started</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('supervisor.research-progress.edit', $student) }}" class="btn btn-primary btn-sm">
                                                View Progress
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
