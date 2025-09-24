{{-- resources/views/teacher/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2 me-2"></i>Teacher Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('teacher.challenge.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>New Challenge
            </a>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="triggerClustering()">
                <i class="bi bi-diagram-3 me-1"></i>Run Clustering
            </button>
        </div>
    </div>
</div>

<!-- Summary Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $studentCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-2 text-primary"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Challenges</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $challengeCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-puzzle fs-2 text-success"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Attempts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAttempts) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-play-circle fs-2 text-info"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Success Rate</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($successRate, 1) }}%</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $successRate }}%"></div>
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
    <!-- Recent Activity -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-activity me-2"></i>Recent Student Activity
                </h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <div class="dropdown-header">Filter Options:</div>
                        <a class="dropdown-item" href="?filter=today">Today Only</a>
                        <a class="dropdown-item" href="?filter=week">This Week</a>
                        <a class="dropdown-item" href="?filter=all">All Time</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($recentAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Challenge</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($attempt->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $attempt->user->name }}</div>
                                                    <small class="text-muted">{{ $attempt->user->student_id }}</small>
                                                </div>
                                            </div>
                                        </td>
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
                                                <div class="progress" style="width: 50px;">
                                                    <div class="progress-bar {{ $attempt->score >= 70 ? 'bg-success' : ($attempt->score >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                                         style="width: {{ $attempt->score }}%"></div>
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
                                            <small class="text-muted">
                                                {{ $attempt->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('teacher.student.detail', $attempt->user->id) }}" 
                                                   class="btn btn-outline-primary">
                                                    <i class="bi bi-person"></i>
                                                </a>
                                                <button class="btn btn-outline-info" 
                                                        onclick="viewAttemptDetails({{ $attempt->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
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
                        <h5>No recent activity</h5>
                        <p>Students haven't submitted any solutions recently.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Clustering Insights & Quick Actions -->
    <div class="col-xl-4 col-lg-5">
        <!-- Clustering Insights -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-diagram-3 me-2"></i>Clustering Insights
                    <button class="btn btn-sm btn-outline-primary float-end" onclick="triggerClustering()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </h6>
            </div>
            <div class="card-body" id="clusteringContent">
                <div class="text-center text-muted">
                    <i class="bi bi-diagram-3 fs-1 mb-3"></i>
                    <p>Click "Run Clustering" to analyze student learning patterns.</p>
                    <button class="btn btn-primary btn-sm" onclick="triggerClustering()">
                        <i class="bi bi-play-circle me-1"></i>Run Analysis
                    </button>
                </div>
                {{-- TODO: Connect to FastAPI /cluster endpoint to show actual clustering results --}}
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('teacher.challenge.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create New Challenge
                    </a>
                    <a href="{{ route('teacher.students') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i>View All Students
                    </a>
                    <a href="{{ route('teacher.analytics') }}" class="btn btn-outline-info">
                        <i class="bi bi-graph-up me-2"></i>View Analytics
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-pie-chart me-2"></i>Performance Distribution
                </h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Students Overview Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-people me-2"></i>Students Overview
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="studentsTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Progress</th>
                        <th>Success Rate</th>
                        <th>Last Active</th>
                        <th>Learning Style</th>
                        <th>Cluster</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through students data (would come from controller) --}}
                    {{-- TODO: Replace with actual students data from controller --}}
                    @forelse($students ?? [] as $student)
                        @php
                            $profile = $student->learnerProfile;
                            $successRate = $profile && $profile->total_attempts > 0 
                                ? round(($profile->successful_attempts / $profile->total_attempts) * 100) 
                                : 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $student->name }}</div>
                                        <small class="text-muted">{{ $student->student_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($profile)
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">{{ number_format($profile->overall_performance, 1) }}%</div>
                                        <div class="progress" style="width: 80px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ $profile->overall_performance }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">Not started</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $successRate >= 70 ? 'success' : ($successRate >= 40 ? 'warning' : 'danger') }}">
                                    {{ $successRate }}%
                                </span>
                            </td>
                            <td>
                                @if($profile && $profile->last_active_date)
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($profile->last_active_date)->diffForHumans() }}
                                    </small>
                                @else
                                    <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td>
                                @if($profile)
                                    <span class="badge bg-info">{{ ucfirst($profile->learning_style ?? 'Unknown') }}</span>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td>
                                @if($profile && $profile->cluster)
                                    <span class="badge bg-secondary">{{ $profile->cluster }}</span>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('teacher.student.detail', $student->id) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-person"></i>
                                    </a>
                                    @if($profile)
                                        <button class="btn btn-outline-info" 
                                                onclick="showStudentInsights({{ $student->id }})">
                                            <i class="bi bi-graph-up"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-people fs-1 mb-3"></i>
                                <div>No students enrolled yet.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Attempt Details Modal -->
<div class="modal fade" id="attemptDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-code-slash me-2"></i>Attempt Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attemptDetailsContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Student Insights Modal -->
<div class="modal fade" id="studentInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-lines-fill me-2"></i>Student Insights
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentInsightsContent">
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
    
    // Auto-refresh recent activity every 30 seconds
    setInterval(refreshRecentActivity, 30000);
});

function initPerformanceChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    // Sample data - would be populated from controller
    const performanceData = {
        labels: ['Excellent (90-100%)', 'Good (70-89%)', 'Average (50-69%)', 'Needs Help (<50%)'],
        datasets: [{
            data: [15, 35, 30, 20], // Sample percentages
            backgroundColor: [
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: performanceData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                }
            },
            cutout: '60%'
        }
    });
}

function triggerClustering() {
    const content = document.getElementById('clusteringContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Running clustering analysis...</span>
            </div>
            <p class="mt-2 mb-0">Analyzing student learning patterns...</p>
        </div>
    `;
    
    // Call FastAPI clustering endpoint
    fetch('/api/trigger-clustering', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            min_clusters: 3,
            max_clusters: 6
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayClusteringResults(data.clusters);
        } else {
            content.innerHTML = `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${data.message || 'Clustering analysis failed.'}
                </div>
            `;
        }
    })
    .catch(error => {
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error running clustering analysis.
            </div>
        `;
    });
}

function displayClusteringResults(clusters) {
    const content = document.getElementById('clusteringContent');
    let html = '<div class="cluster-results">';
    
    clusters.forEach((cluster, index) => {
        const characteristics = cluster.characteristics || {};
        html += `
            <div class="mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Cluster ${cluster.cluster_id + 1}</h6>
                    <span class="badge bg-primary">${cluster.size} students</span>
                </div>
                <div class="small text-muted">
                    <div><strong>Cognitive:</strong> ${characteristics.cognitive || 'N/A'}</div>
                    <div><strong>Behavioral:</strong> ${characteristics.behavioral || 'N/A'}</div>
                    <div><strong>Recommendation:</strong> ${characteristics.recommendation || 'Continue monitoring'}</div>
                </div>
            </div>
        `;
    });
    
    html += `
        <div class="text-center mt-3">
            <a href="{{ route('teacher.clusters') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-diagram-3 me-1"></i>View Detailed Analysis
            </a>
        </div>
    `;
    html += '</div>';
    
    content.innerHTML = html;
}

function viewAttemptDetails(attemptId) {
    const modal = new bootstrap.Modal(document.getElementById('attemptDetailsModal'));
    const content = document.getElementById('attemptDetailsContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Load attempt details
    fetch(`/api/teacher/attempt/${attemptId}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Student Information</h6>
                        <p><strong>Name:</strong> ${data.student_name}</p>
                        <p><strong>Challenge:</strong> ${data.challenge_title}</p>
                        <p><strong>Score:</strong> ${data.score}%</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-${data.is_successful ? 'success' : 'danger'}">
                                ${data.is_successful ? 'Passed' : 'Failed'}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Performance Metrics</h6>
                        <p><strong>Time Spent:</strong> ${data.time_spent || 'N/A'}</p>
                        <p><strong>Execution Time:</strong> ${data.execution_time || 'N/A'}ms</p>
                        <p><strong>Memory Used:</strong> ${data.memory_used || 'N/A'}KB</p>
                    </div>
                </div>
                
                <hr>
                
                <h6>Submitted Code</h6>
                <pre class="bg-light p-3"><code>${data.code}</code></pre>
                
                ${data.error_message ? `
                    <h6 class="text-danger">Error Message</h6>
                    <div class="alert alert-danger">
                        <pre class="mb-0">${data.error_message}</pre>
                    </div>
                ` : ''}
                
                ${data.ai_feedback ? `
                    <h6>AI Feedback</h6>
                    <div class="alert alert-info">
                        ${data.ai_feedback.message || 'No specific feedback available.'}
                    </div>
                ` : ''}
            `;
            
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load attempt details.
                </div>
            `;
        });
}

function showStudentInsights(studentId) {
    const modal = new bootstrap.Modal(document.getElementById('studentInsightsModal'));
    const content = document.getElementById('studentInsightsContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading insights...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Load student insights
    fetch(`/api/teacher/student/${studentId}/insights`)
        .then(response => response.json())
        .then(data => {
            // Display comprehensive student insights
            const html = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Learning Profile</div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Overall Performance:</strong> ${data.overall_performance}%
                                </div>
                                <div class="mb-2">
                                    <strong>Learning Style:</strong> ${data.learning_style}
                                </div>
                                <div class="mb-2">
                                    <strong>Current Cluster:</strong> 
                                    <span class="badge bg-secondary">${data.cluster || 'Unassigned'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Recent Activity</div>
                            <div class="card-body">
                                <!-- Recent attempts table would go here -->
                                <p>Detailed activity analysis and recommendations...</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load student insights.
                </div>
            `;
        });
}

function refreshRecentActivity() {
    // Auto-refresh recent activity (optional)
    // This would reload the recent attempts section
    console.log('Refreshing recent activity...');
    // Implementation would fetch updated data and update the table
}

/*
TODO: Advanced Teacher Dashboard Features

1. Real-time notifications for student submissions
2. Advanced filtering and sorting options
3. Export functionality for student data
4. Bulk operations (assign challenges, send messages)
5. Integration with external LMS systems
6. Advanced analytics with drill-down capabilities
*/
</script>
@endpush