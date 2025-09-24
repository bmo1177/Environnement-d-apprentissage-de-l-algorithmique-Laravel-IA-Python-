{{-- resources/views/teacher/student-detail.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('teacher.students') }}">Students</a></li>
                <li class="breadcrumb-item active">{{ $student->name }}</li>
            </ol>
        </nav>
        <h1 class="h2 mb-0">Student Profile: {{ $student->name }}</h1>
    </div>
</div>

<div class="row">
    <!-- Student Overview -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-body text-center">
                <div class="bg-primary text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" 
                     style="width: 80px; height: 80px;">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                <h4>{{ $student->name }}</h4>
                <p class="text-muted">{{ $student->student_id }}</p>
                <p class="text-muted">{{ $student->email }}</p>
                
                @if(isset($stats))
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="mb-0">{{ $stats['total_attempts'] }}</h5>
                                <small class="text-muted">Total Attempts</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="mb-0">{{ $stats['successful_attempts'] }}</h5>
                            <small class="text-muted">Successful</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Learning Profile Summary -->
        @if($student->learnerProfile)
            @php $profile = $student->learnerProfile; @endphp
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Learning Profile</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Overall Performance</span>
                            <span class="fw-bold">{{ number_format($profile->overall_performance, 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width: {{ $profile->overall_performance }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Engagement Level</span>
                            <span class="fw-bold">{{ number_format($profile->engagement_level, 1) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $profile->engagement_level }}%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="text-muted small">Learning Style</div>
                            <div class="fw-bold">{{ ucfirst($profile->learning_style) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Pace</div>
                            <div class="fw-bold">{{ ucfirst($profile->pace) }}</div>
                        </div>
                    </div>
                    
                    @if($profile->cluster)
                        <div class="text-center mt-3">
                            <span class="badge bg-info">Cluster: {{ $profile->cluster }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Detailed Analytics -->
    <div class="col-lg-8">
        <!-- Performance Chart -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Performance Over Time</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" height="100"></canvas>
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Recent Attempts</h6>
            </div>
            <div class="card-body">
                @if(isset($recentAttempts) && $recentAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Challenge</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Time Spent</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $attempt->challenge->title }}</div>
                                                <span class="badge bg-{{ $attempt->challenge->difficulty === 'easy' ? 'success' : ($attempt->challenge->difficulty === 'medium' ? 'warning' : 'danger') }} badge-sm">
                                                    {{ ucfirst($attempt->challenge->difficulty) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $attempt->score }}%</span>
                                                <div class="progress" style="width: 60px;">
                                                    <div class="progress-bar {{ $attempt->score >= 70 ? 'bg-success' : ($attempt->score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                         style="width: {{ $attempt->score }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($attempt->is_successful)
                                                <span class="badge bg-success">Passed</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($attempt->time_spent)
                                                {{ gmdate("H:i:s", $attempt->time_spent) }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $attempt->created_at->format('M j, Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewAttemptCode({{ $attempt->id }})">
                                                    <i class="bi bi-code"></i>
                                                </button>
                                                @if($attempt->ai_feedback)
                                                    <button class="btn btn-outline-info" onclick="viewAiFeedback({{ $attempt->id }})">
                                                        <i class="bi bi-robot"></i>
                                                    </button>
                                                @endif
                                                @if($attempt->heatmapLines->count() > 0)
                                                    <a href="{{ route('heatmap.generate', $attempt) }}" class="btn btn-outline-secondary">
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
                        <i class="bi bi-clock-history fs-1 mb-3"></i>
                        <h5>No attempts yet</h5>
                        <p>This student hasn't submitted any solutions yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Attempt Code Modal -->
<div class="modal fade" id="attemptCodeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-code-slash me-2"></i>Submitted Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attemptCodeContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- AI Feedback Modal -->
<div class="modal fade" id="aiFeedbackModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-robot me-2"></i>AI Feedback Analysis
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="aiFeedbackContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize performance chart
    initPerformanceChart();
});

function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Sample data - in production, this would come from the backend
    const performanceData = {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Current'],
        datasets: [
            {
                label: 'Performance Score',
                data: [20, 45, 60, 75, {{ $student->learnerProfile->overall_performance ?? 50 }}],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Engagement Level',
                data: [30, 40, 65, 70, {{ $student->learnerProfile->engagement_level ?? 50 }}],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    };
    
    new Chart(ctx, {
        type: 'line',
        data: performanceData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Student Progress Trend'
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
            }
        }
    });
}

function viewAttemptCode(attemptId) {
    const modal = new bootstrap.Modal(document.getElementById('attemptCodeModal'));
    const content = document.getElementById('attemptCodeContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Load attempt code
    fetch(`/api/teacher/attempt/${attemptId}/code`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="mb-3">
                    <h6>Challenge: ${data.challenge_title}</h6>
                    <div class="d-flex justify-content-between">
                        <span>Score: <strong>${data.score}%</strong></span>
                        <span>Status: <span class="badge bg-${data.is_successful ? 'success' : 'danger'}">${data.is_successful ? 'Passed' : 'Failed'}</span></span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6>Submitted Code:</h6>
                    <pre class="bg-light p-3 border rounded" style="max-height: 400px; overflow-y: auto;"><code>${data.code}</code></pre>
                </div>
            `;
            
            if (data.error_message) {
                html += `
                    <div class="alert alert-danger">
                        <h6>Error Message:</h6>
                        <pre class="mb-0">${data.error_message}</pre>
                    </div>
                `;
            }
            
            if (data.test_results && data.test_results.length > 0) {
                html += `
                    <h6>Test Results:</h6>
                    <div class="row">
                `;
                data.test_results.forEach((result, index) => {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="card ${result.passed ? 'border-success' : 'border-danger'}">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Test ${index + 1}</span>
                                        <span class="badge bg-${result.passed ? 'success' : 'danger'}">
                                            ${result.passed ? 'PASS' : 'FAIL'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load attempt code.
                </div>
            `;
        });
}

function viewAiFeedback(attemptId) {
    const modal = new bootstrap.Modal(document.getElementById('aiFeedbackModal'));
    const content = document.getElementById('aiFeedbackContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Load AI feedback
    fetch(`/api/teacher/attempt/${attemptId}/feedback`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.feedback && data.feedback.message) {
                html += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Primary Feedback</h6>
                        </div>
                        <div class="card-body">
                            <p>${data.feedback.message}</p>
                            ${data.feedback.severity ? `<span class="badge bg-${data.feedback.severity === 'high' ? 'danger' : data.feedback.severity === 'medium' ? 'warning' : 'info'}">${data.feedback.severity.toUpperCase()}</span>` : ''}
                        </div>
                    </div>
                `;
            }
            
            if (data.hints && data.hints.length > 0) {
                html += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Hints Provided</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                `;
                data.hints.forEach(hint => {
                    html += `<li class="mb-2"><i class="bi bi-arrow-right me-2"></i>${hint}</li>`;
                });
                html += `
                            </ul>
                        </div>
                    </div>
                `;
            }
            
            if (data.polya_guidance) {
                html += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Polya Strategy Guidance</h6>
                        </div>
                        <div class="card-body">
                `;
                Object.keys(data.polya_guidance).forEach(phase => {
                    html += `
                        <div class="mb-2">
                            <strong>${phase.charAt(0).toUpperCase() + phase.slice(1)}:</strong>
                            <p class="mb-0">${data.polya_guidance[phase]}</p>
                        </div>
                    `;
                });
                html += `
                        </div>
                    </div>
                `;
            }
            
            if (data.resources && data.resources.length > 0) {
                html += `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Recommended Resources</h6>
                        </div>
                        <div class="card-body">
                `;
                data.resources.forEach(resource => {
                    html += `
                        <div class="mb-2">
                            <i class="bi bi-link-45deg me-2"></i>
                            <a href="${resource.url}" target="_blank">${resource.title}</a>
                            <span class="badge bg-light text-dark ms-2">${resource.type}</span>
                        </div>
                    `;
                });
                html += `
                        </div>
                    </div>
                `;
            }
            
            content.innerHTML = html || `
                <div class="text-center text-muted">
                    <i class="bi bi-info-circle fs-1 mb-3"></i>
                    <p>No AI feedback available for this attempt.</p>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load AI feedback.
                </div>
            `;
        });
}

/*
TODO: Advanced Student Detail Features

1. Real-time progress tracking
2. Detailed competency breakdown
3. Mistake pattern analysis
4. Peer comparison (anonymized)
5. Learning path recommendations
6. Direct messaging with student
7. Assignment scheduling
8. Performance prediction models
*/
</script>
@endpush