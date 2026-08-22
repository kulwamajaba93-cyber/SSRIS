@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Submit New Document</h1>
                <a href="{{ route('student.documents.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Documents
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-file-upload"></i> Upload Document</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.documents.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Document Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required autofocus>
                            @error('title')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description/Abstract *</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">Upload File (PDF, DOC, DOCX) *</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Maximum file size: 10MB</div>
                            @error('file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> This will be saved as a new version. Previous versions will remain accessible.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('student.documents.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i> Submit Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
