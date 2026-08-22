@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Messages</h1>
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if($supervisor)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Conversation with {{ $supervisor->name }}</h5>
                </div>
                <div class="card-body">
                    <div id="chat-messages" class="chat-container" style="max-height: 500px; overflow-y: auto;"
                         data-last-message-id="{{ $messages->max('id') ?? 0 }}">
                        @if($messages->count() > 0)
                            @foreach($messages->reverse() as $message)
                                <div class="message mb-3 @if($message->sender_id === auth()->id()) text-end @else text-start @endif" data-message-id="{{ $message->id }}">
                                    <div class="d-inline-block @if($message->sender_id === auth()->id()) bg-primary text-white @else bg-light @endif p-3 rounded" style="max-width: 70%;">
                                        <p class="mb-1">{{ $message->message }}</p>
                                        <small class="@if($message->sender_id === auth()->id()) text-white-50 @else text-muted @endif">
                                            {{ $message->created_at->format('M j, Y g:i A') }}
                                            @if($message->sender_id !== auth()->id() && !$message->read)
                                                <span class="badge bg-success ms-1">New</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 chat-empty-state">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No messages yet. Start the conversation!</p>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('student.messages.store') }}">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control" name="message" placeholder="Type your message..." required autofocus>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                You need to be assigned a supervisor to send messages. Please contact the administrator.
            </div>
        </div>
    </div>
    @endif
</div>
@include('components.message-chat-poll', ['pollUrl' => route('messages.poll')])
@endsection
