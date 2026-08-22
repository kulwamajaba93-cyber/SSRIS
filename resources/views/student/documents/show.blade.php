@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Document Details</h1>
                <div>
                    @if(!$document->isReviewed())
                        <form action="{{ route('student.documents.destroy', $document) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger me-2" onclick="return confirm('Are you sure you want to remove this document?')">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('student.documents.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> {{ $document->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-secondary">{{ $document->version_display }}</span>
                        <span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span>
                    </div>
                    
                    <h6>Description</h6>
                    <p>{{ $document->abstract }}</p>
                    
                    @if($document->submission_notes)
                        <h6>Submission Notes</h6>
                        <p>{{ $document->submission_notes }}</p>
                    @endif

                    @if($document->review_comments)
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-comment me-2"></i>Reviewer Comments</h6>
                            <p>{{ $document->review_comments }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($document->file_path)
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-download"></i> Download File</h5>
                </div>
                <div class="card-body">
                    <p><strong>Filename:</strong> {{ $document->original_filename }}</p>
                    <a href="{{ route('student.documents.download', $document) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i> Download Document
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Document Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Version:</strong></td>
                            <td>{{ $document->version_display }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><span class="badge bg-{{ $document->status_badge_color }}">{{ $document->status_label }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Submitted:</strong></td>
                            <td>{{ $document->submitted_at ? $document->submitted_at->format('M j, Y g:i A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Reviewed:</strong></td>
                            <td>{{ $document->reviewed_at ? $document->reviewed_at->format('M j, Y') : 'N/A' }}</td>
                        </tr>
                        @if($document->reviewer)
                        <tr>
                            <td><strong>Reviewed By:</strong></td>
                            <td>{{ $document->reviewer->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($document->feedback->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Feedback</h5>
                </div>
                <div class="card-body">
                    @foreach($document->feedback as $item)
                        <div class="mb-3 pb-3 border-bottom">
                            <small class="text-muted">{{ $item->created_at->format('M j, Y') }}</small>
                            <p class="mb-1">{{ $item->comments }}</p>
                            <small>By: {{ $item->supervisor->name }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
