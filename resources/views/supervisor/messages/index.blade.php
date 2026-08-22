@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Messages</h1>
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
                    <h5 class="mb-0"><i class="fas fa-users"></i> Student Conversations</h5>
                </div>
                <div class="card-body">
                    @if(count($studentsWithUnread) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Registration Number</th>
                                        <th>Program</th>
                                        <th>Unread Messages</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentsWithUnread as $item)
                                        <tr>
                                            <td>{{ $item['student']->name }}</td>
                                            <td>{{ $item['student']->username }}</td>
                                            <td>{{ $item['student']->program }}</td>
                                            <td>
                                                @if($item['unread_count'] > 0)
                                                    <span class="message-notification-badge message-notification-badge-inline">{{ $item['unread_count'] > 99 ? '99+' : $item['unread_count'] }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('supervisor.messages.show', $item['student']) }}" class="btn btn-sm btn-primary position-relative">
                                                    <i class="fas fa-comments"></i> View Conversation
                                                    @if($item['unread_count'] > 0)
                                                        <span class="message-notification-badge">{{ $item['unread_count'] > 99 ? '99+' : $item['unread_count'] }}</span>
                                                    @endif
                                                </a>
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
</div>
@endsection
