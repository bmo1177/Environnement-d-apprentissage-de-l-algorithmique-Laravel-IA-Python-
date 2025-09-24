{{-- resources/views/student/profile.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-circle me-2"></i>My Learning Profile
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-speedometer2 me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

@if($profile)
    <!-- Profile Overview Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card text-center shadow">
                <div class="card-body">
                    <div class="mb-3">
                        <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill fs-1 text-white"></i>
                        </div>
                    </div>
                    <h4>{{ auth()->user()->name }}</h4>
                    <p class="text-muted">{{ auth()->user()->student_id ?? 'Student' }}</p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="mb-0">{{ $profile->streak_days }}</h5>
                                <small class="text-muted">Day Streak</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-0">{{ number_format($profile->overall_performance, 1) }}%</h5>
                            <small class="text-muted">Overall Score</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Dimensions -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Learning Dimensions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Cognitive Dimension -->
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary">
                                <i class="bi bi-brain me-2"></i>Cognitive Profile
                            </h6>
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Problem Solving</span>
                                    <span>{{ number_format($profile->problem_solving_score, 1) }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $profile->problem_solving_score }}%"></div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Logical Reasoning</span>
                                    <span>{{ number_format($profile->logical_reasoning_score, 1) }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $profile->logical_reasoning_score }}%"></div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Pattern Recognition</span>
                                    <span>{{ number_format($profile->pattern_recognition_score, 1) }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $profile->pattern_recognition_score }}%"></div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Abstraction</span>
                                    <span>{{ number_format($profile->abstraction_score, 1) }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ $profile->abstraction_score }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Behavioral & Motivational -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="bi bi-activity me-2"></i>Behavioral Profile
                            </h6>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="fw-bold fs-5">{{ $profile->total_attempts }}</div>
                                    <small class="text-muted">Total Attempts</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold fs-5">{{ $profile->successful_attempts }}</div>
                                    <small class="text-muted">Successful</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold fs-5">
                                        @php
                                            $successRate = $profile->total_attempts > 0 
                                                ? round(($profile->successful_attempts / $profile->total_attempts) * 100) 
                                                : 0;
                                        @endphp
                                        {{ $successRate }}%
                                    </div>
                                    <small class="text-muted">Success Rate</small>
                                </div>
                            </div>

                            <h6 class="text-warning mb-2">
                                <i class="bi bi-heart me-2"></i>Motivational Profile
                            </h6>
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Engagement Level</span>
                                    <span>{{ number_format($profile->engagement_level, 1) }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" style="width: {{ $profile->engagement_level }}%"></div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Persistence Score</span>
                                    <span>{{ number_format($profile->persistence_score, 1) }}%</span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" style="width: {{ $profile->persistence_score }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Competency Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>Competency Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($competencyData))
                        <div class="row">
                            @foreach($competencyData as $competency)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $competency['name'] }}</h6>
                                            <small class="text-muted">{{ $competency['domain'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold {{ $competency['score'] >= 70 ? 'text-success' : ($competency['score'] >= 40 ? 'text-warning' : 'text-danger') }}">
                                                {{ $competency['score'] }}%
                                            </span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar {{ $competency['score'] >= 70 ? 'bg-success' : ($competency['score'] >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $competency['score'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-graph-up fs-1 mb-3"></i>
                            <p>Start solving challenges to build your competency profile!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Learning Style & Preferences -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-palette me-2"></i>Learning Style
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px;">
                            <i class="bi bi-person-gear fs-3 text-info"></i>
                        </div>
                        <h6>{{ ucfirst($profile->learning_style) }} Learner</h6>
                        <p class="text-muted small mb-2">Preferred pace: {{ ucfirst($profile->pace) }}</p>
                        
                        @if($profile->average_time_per_challenge > 0)
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">Average time per challenge:</small>
                                <div class="fw-bold">{{ gmdate("H:i:s", $profile->average_time_per_challenge) }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Achievements -->
            @if($profile->achievements && count($profile->achievements) > 0)
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-award me-2"></i>Achievements
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($profile->achievements as $achievement)
                                <div class="col-6 text-center mb-3">
                                    @php
                                        $badgeInfo = match($achievement) {
                                            'week_streak' => ['icon' => 'fire', 'color' => 'warning', 'title' => '7-Day Streak'],
                                            'perfect_score' => ['icon' => 'trophy-fill', 'color' => 'success', 'title' => 'Perfect Score'],
                                            'quick_solver' => ['icon' => 'lightning-fill', 'color' => 'primary', 'title' => 'Quick Solver'],
                                            'persistent' => ['icon' => 'heart-fill', 'color' => 'danger', 'title' => 'Persistent'],
                                            default => ['icon' => 'star-fill', 'color' => 'secondary', 'title' => ucfirst($achievement)]
                                        };
                                    @endphp
                                    <div class="bg-{{ $badgeInfo['color'] }} bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-{{ $badgeInfo['icon'] }} text-{{ $badgeInfo['color'] }}"></i>
                                    </div>
                                    <small class="fw-bold">{{ $badgeInfo['title'] }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Progress Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Progress Over Time
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="progressChart" height="100"></canvas>
                    {{-- TODO: Add Chart.js to display progress over time --}}
                </div>
            </div>
        </div>
    </div>

@else
    <!-- No Profile State -->
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-person-plus fs-1 text-muted mb-3"></i>
                <h4 class="text-muted">Profile Not Available</h4>
                <p class="text-muted">Your learning profile will be created after you complete your first challenge.</p>
                <a href="{{ route('student.challenges') }}" class="btn btn-primary">
                    <i class="bi bi-puzzle me-2"></i>Start Your First Challenge
                </a>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if($profile)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Progress Chart
    const ctx = document.getElementById('progressChart').getContext('2d');
    
    // Sample data - in production, this would come from the backend
    const progressData = {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Current'],
        datasets: [
            {
                label: 'Overall Performance',
                data: [20, 35, 45, 60, {{ $profile->overall_performance }}],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Success Rate',
                data: [15, 30, 50, 65, {{ $successRate }}],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: progressData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Learning Progress Trend'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            elements: {
                point: {
                    radius: 4,
                    hoverRadius: 6
                }
            }
        }
    });
    
    /*
    TODO: Advanced Profile Features
    
    1. Fetch real progress data from API:
       fetch('/api/student/progress-data')
           .then(response => response.json())
           .then(data => {
               // Update chart with real data
               progressChart.data = data;
               progressChart.update();
           });
    
    2. Learning style quiz integration
    3. Goal setting and tracking
    4. Peer comparison (anonymized)
    5. Detailed competency breakdown with skill trees
    */
});
</script>
@endif
@endpush