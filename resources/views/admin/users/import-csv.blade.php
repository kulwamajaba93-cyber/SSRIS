@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Import Students from Excel</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-excel"></i> Upload Excel File</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.process-csv') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="csv_file" class="form-label">Excel File (.xlsx, .xls) *</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".xlsx,.xls,.csv" required>
                            @if($errors->has('csv_file'))
                                <div class="text-danger">{{ $errors->first('csv_file') }}</div>
                            @endif
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Excel Format</h6>
                            <p class="mb-2">Your Excel file should have the following columns (first row as header):</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Programme</th>
                                            <th>Registration Number</th>
                                            <th>Phone Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Kulwa Mangu Majaba</td>
                                            <td>BBICT</td>
                                            <td>MOCU/BBICT/1089/23</td>
                                            <td>0699889430</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mb-0 mt-2"><strong>Note:</strong> The first row should be the header. Passwords will be auto-generated using the format: <code>mocu.programme.number.year</code></p>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                            <ul class="mb-0">
                                <li>Programme must be: <strong>BBICT</strong>, <strong>BHRM</strong>, or <strong>BAT</strong></li>
                                <li>Registration Number format: <strong>MOCU/PROGRAM/NUMBER/YEAR</strong> (e.g., MOCU/BBICT/1089/23)</li>
                                <li>Duplicate registration numbers will be skipped</li>
                                <li>Passwords will be auto-generated and cannot be changed</li>
                            </ul>
                        </div>

                        @if(session('importErrors'))
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-circle me-2"></i>Import Errors</h6>
                                <ul class="mb-0">
                                    @foreach(session('importErrors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload me-2"></i> Import Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
