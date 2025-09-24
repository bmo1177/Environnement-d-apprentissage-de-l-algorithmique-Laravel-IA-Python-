{{-- resources/views/student/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2 me-2"></i>My Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('student.challenges') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-puzzle me-1"></i>Browse Challenges
            </a>
            <a href="{{ route('student.profile') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-person me-1"></i>View Profile
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Attempts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_attempts'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-play-circle fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Success Rate</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['success_rate'] }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Current Streak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['current_streak'] }} days</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-fire fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Overall Score</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $stats['overall_score'] }}%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $stats['overall_score'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-trophy fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Progress Visualization -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-graph-up me-2"></i>Progress Overview
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="progressChart" width="200" height="200"></canvas>
                        {{-- Placeholder for CodeMirror/Monaco: Replace canvas with dynamic editor visualization --}}
                    </div>
                    <div class="col-md-6">
                        <!-- Competency Progress -->
                        <h6 class="font-weight-bold mb-3">Competency Progress</h6>
                        @foreach($competencies as $competency)
                            @php
                                $score = $profile && $profile->competency_scores 
                                    ? ($profile->competency_scores[$competency->id] ?? 0) 
                                    : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-sm font-weight-bold">{{ $competency->name }}</span>
                                    <span class="text-sm">{{ $score }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar {{ $score >= 70 ? 'bg-success' : ($score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                         role="progressbar" style="width: {{ $score }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations & Heatmap -->
    <div class="col-lg-4 mb-4">
        <!-- AI Recommendations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightbulb me-2"></i>AI Recommendations
                </h6>
            </div>
            <div class="card-body">
                @if(!empty($recommendations))
                    <ul class="list-unstyled mb-0">
                        @foreach($recommendations as $recommendation)
                            <li class="mb-2">
                                <i class="bi bi-arrow-right-circle me-2 text-primary"></i>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-check-circle fs-1 mb-2"></i>
                        <p>Great job! Keep solving challenges to get personalized recommendations.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Heatmap Placeholder -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar3 me-2"></i>Activity Heatmap
                </h6>
            </div>
            <div class="card-body">
                <div class="heatmap-placeholder p-4 text-center" style="height: 150px;">
                    <i class="bi bi-calendar-check fs-1 text-muted mb-2"></i>
                    <p class="text-muted mb-0">Activity heatmap visualization</p>
                    <small class="text-muted">
                        {{-- TODO: Integrate with heatmap generation from FastAPI /heatmap/{challengeId} --}}
                        Connect to Python service endpoint for dynamic heatmap
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attempts -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-clock-history me-2"></i>Recent Attempts
        </h6>
    </div>
    <div class="card-body">
        @if($recentAttempts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Challenge</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Time Spent</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttempts as $attempt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="badge bg-{{ $attempt->challenge->difficulty === 'easy' ? 'success' : ($attempt->challenge->difficulty === 'medium' ? 'warning' : 'danger') }} me-2">
                                            {{ ucfirst($attempt->challenge->difficulty) }}
                                        </div>
                                        <strong>{{ $attempt->challenge->title }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">{{ $attempt->score }}%</div>
                                        <div class="progress" style="width: 60px;">
                                            <div class="progress-bar {{ $attempt->score >= 70 ? 'bg-success' : ($attempt->score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" style="width: {{ $attempt->score }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($attempt->is_successful)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Passed
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Failed
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($attempt->time_spent)
                                        {{ gmdate("H:i:s", $attempt->time_spent) }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $attempt->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('student.challenge', $attempt->challenge) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($attempt->heatmapLines->count() > 0)
                                            <a href="{{ route('heatmap.generate', $attempt) }}" 
                                               class="btn btn-outline-info">
                                                <i class="bi bi-map"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 mb-3"></i>
                <h5>No attempts yet</h5>
                <p>Start solving challenges to see your progress here!</p>
                <a href="{{ route('student.challenges') }}" class="btn btn-primary">
                    <i class="bi bi-puzzle me-2"></i>Browse Challenges
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Progress Chart (Doughnut)
    const ctx = document.getElementById('progressChart').getContext('2d');
    
    const progressChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Remaining'],
            datasets: [{
                data: [{{ $stats['overall_score'] }}, {{ 100 - $stats['overall_score'] }}],
                backgroundColor: [
                    '#4e73df',
                    '#e3e6f0'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Overall Progress'
                }
            },
            cutout: '60%'
        }
    });
    
    // Add percentage in center
    const originalDoughnutDraw = Chart.controllers.doughnut.prototype.draw;
    Chart.helpers.extend(Chart.controllers.doughnut.prototype, {
        draw: function() {
            originalDoughnutDraw.apply(this, arguments);
            
            const chart = this.chart;
            const ctx = chart.ctx;
            const width = chart.width;
            const height = chart.height;
            
            ctx.restore();
            const fontSize = (height / 114).toFixed(2);
            ctx.font = fontSize + "em sans-serif";
            ctx.fillStyle = "#4e73df";
            ctx.textBaseline = "middle";
            
            const text = "{{ $stats['overall_score'] }}%";
            const textX = Math.round((width - ctx.measureText(text).width) / 2);
            const textY = height / 2;
            
            ctx.fillText(text, textX, textY);
            ctx.save();
        }
    });
    
    /*
    TODO: Heatmap Integration
    Replace heatmap placeholder with actual data from FastAPI
    
    Example implementation:
    fetch('/api/heatmap/data?user_id={{ auth()->id() }}')
        .then(response => response.json())
        .then(data => {
            // Render heatmap using data
            // Consider libraries like cal-heatmap or custom implementation
        });
    */
});
</script>
@endpush