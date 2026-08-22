@extends('layouts.student')

@section('title', 'Research Progress')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Research Progress</h1>

        <div class="card">
            <div class="card-header">
                Your Research Journey
            </div>
            <div class="card-body">
                <div class="progress-container">
                    @foreach($stages as $stage)
                        @php
                            $progressItem = $progress[$stage->id] ?? null;
                            $status = $progressItem ? $progressItem->status : 'locked';
                        @endphp
                        <div class="progress-step @if($status === 'approved') completed @elseif($status === 'pending') active @endif">
                            <div class="progress-circle">
                                @if($status === 'approved')
                                    <i class="fas fa-check"></i>
                                @elseif($status === 'pending')
                                    <span class="step-number">{{ $stage->step_number }}</span>
                                @else
                                    <i class="fas fa-lock"></i>
                                @endif
                            </div>
                            <div class="progress-content">
                                <h5 class="progress-title">{{ $stage->name }}</h5>
                                <p class="progress-status text-capitalize">
                                    @if($stage->name === 'Completed' && $status === 'approved')
                                        Completed
                                    @else
                                        {{ $status }}
                                    @endif
                                </p>
                                @if($status === 'approved' && $progressItem->approved_at)
                                    <small class="text-muted">
                                        @if($stage->name === 'Completed')
                                            Completed on {{ $progressItem->approved_at->format('M d, Y') }}
                                        @else
                                            Approved on {{ $progressItem->approved_at->format('M d, Y') }}
                                        @endif
                                    </small>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="progress-line @if($status === 'approved') completed @endif"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            padding: 20px 0;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20%;
            position: relative;
            z-index: 1;
        }

        .progress-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .progress-step.completed .progress-circle {
            background-color: #28a745;
        }

        .progress-step.active .progress-circle {
            background-color: #667eea;
        }

        .progress-content {
            text-align: center;
        }

        .progress-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .progress-status {
            color: #666;
            font-size: 14px;
        }

        .progress-line {
            position: absolute;
            top: 30px;
            left: 50%;
            width: 100%;
            height: 4px;
            background-color: #e0e0e0;
            z-index: -1;
        }

        .progress-step.completed + .progress-step .progress-line,
        .progress-line.completed {
            background-color: #28a745;
        }

        @media (max-width: 768px) {
            .progress-container {
                flex-direction: column;
                gap: 30px;
            }

            .progress-step {
                width: 100%;
            }

            .progress-line {
                top: auto;
                bottom: -30px;
                left: 50%;
                width: 4px;
                height: 30px;
            }
        }
    </style>
@endsection
