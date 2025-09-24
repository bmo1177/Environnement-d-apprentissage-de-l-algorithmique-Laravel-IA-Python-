{{-- resources/views/teacher/students.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people me-2"></i>Students Management
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?filter=active">Active Students</a></li>
                <li><a class="dropdown-item" href="?filter=struggling">Struggling Students</a></li>
                <li><a class="dropdown-item" href="?filter=advanced">Advanced Students</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('teacher.students') }}">All Students</a></li>
            </ul>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-primary" onclick="exportStudentData()">
                <i class="bi bi-download me-1"></i>Export Data
            </button>
        </div>
    </div>
</div>

<!-- Students Grid -->
<div class="row">
    @forelse($students as $student)
        @php
            $profile = $student->learnerProfile;
            $successRate = $profile && $profile->total_attempts > 0 
                ? round(($profile->successful_attempts / $profile->total_attempts) * 100) 
                : 0;
            $isActive = $profile && $profile->last_active_date 
                && \Carbon\Carbon::parse($profile->last_active_date)->isAfter(now()->subWeek());
        @endphp
        
        <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px;">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">{{ $student->name }}</h5>
                                    <p class="text-muted small mb-1">{{ $student->student_id }}</p>
                                    
                                    <!-- Activity Status -->
                                    <div class="mb-2">
                                        @if($isActive)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                        
                                        @if($profile && $profile->cluster)
                                            <span class="badge bg-info ms-1">{{ $profile->cluster }}</span>
                                        @endif
                                        
                                        @if($profile && $profile->learning_style)
                                            <span class="badge bg-outline-secondary ms-1">{{ ucfirst($profile->learning_style) }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    @if($profile && $profile->streak_days > 0)
                                        <div class="text-warning mb-1">
                                            <i class="bi bi-fire"></i> {{ $profile->streak_days }} days
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Progress Metrics -->
                            @if($profile)
                                <div class="row text-center mt-3">
                                    <div class="col-4">
                                        <div class="text-muted small">Overall</div>
                                        <div class="fw-bold {{ $profile->overall_performance >= 70 ? 'text-success' : ($profile->overall_performance >= 40 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($profile->overall_performance, 1) }}%
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Success Rate</div>
                                        <div class="fw-bold {{ $successRate >= 70 ? 'text-success' : ($successRate >= 40 ? 'text-warning' : 'text-danger') }}">
                                            {{ $successRate }}%
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Attempts</div>
                                        <div class="fw-bold">{{ $profile->total_attempts }}</div>
                                    </div>
                                </div>
                                
                                <!-- Competency Progress Bars -->
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Cognitive</small>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: {{ $profile->problem_solving_score }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Motivation</small>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-warning" 
                                                     style="width: {{ $profile->engagement_level }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-clock-history mb-2"></i>
                                    <div>Not started yet</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('teacher.student.detail', $student->id) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                            @if($profile)
                                <button type="button" class="btn btn-outline-info" onclick="showQuickInsights({{ $student->id }})">
                                    <i class="bi bi-graph-up me-1"></i>Insights
                                </button>
                            @endif
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                    type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="sendMessage({{ $student->id }})">
                                        <i class="bi bi-envelope me-2"></i>Send Message
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="assignChallenge({{ $student->id }})">
                                        <i class="bi bi-puzzle me-2"></i>Assign Challenge
                                    </a>
                                </li>
                                @if($profile)
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="resetProgress({{ $student->id }})">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Progress
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted mb-3"></i>
                <h4 class="text-muted">No Students Found</h4>
                <p class="text-muted">Students will appear here once they register for your course.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($students->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $students->appends(request()->query())->links() }}
    </div>
@endif

<!-- Summary Statistics -->
@if($students->count() > 0)
<div class="card shadow mt-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-bar-chart me-2"></i>Class Summary
        </h6>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="text-muted small">Total Students</div>
                <div class="h4 mb-0 font-weight-bold text-primary">{{ $students->total() }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Active This Week</div>
                @php
                    $activeCount = $students->filter(function($student) {
                        return $student->learnerProfile && $student->learnerProfile->last_active_date 
                            && \Carbon\Carbon::parse($student->learnerProfile->last_active_date)->isAfter(now()->subWeek());
                    })->count();
                @endphp
                <div class="h4 mb-0 font-weight-bold text-success">{{ $activeCount }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Average Performance</div>
                @php
                    $avgPerformance = $students->filter(function($student) {
                        return $student->learnerProfile;
                    })->avg(function($student) {
                        return $student->learnerProfile->overall_performance;
                    });
                @endphp
                <div class="h4 mb-0 font-weight-bold text-info">{{ number_format($avgPerformance, 1) }}%</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Need Attention</div>
                @php
                    $needAttention = $students->filter(function($student) {
                        $profile = $student->learnerProfile;
                        return $profile && $profile->overall_performance < 50;
                    })->count();
                @endphp
                <div class="h4 mb-0 font-weight-bold text-warning">{{ $needAttention }}</div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modals -->
<!-- Quick Insights Modal -->
<div class="modal fade" id="quickInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-graph-up me-2"></i>Student Quick Insights
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="quickInsightsContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div class="modal fade" id="sendMessageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope me-2"></i>Send Message to Student
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="messageForm">
                    <input type="hidden" id="messageStudentId" value="">
                    <div class="mb-3">
                        <label for="messageSubject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="messageSubject" required>
                    </div>
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Message</label>
                        <textarea class="form-control" id="messageContent" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitMessage()">Send Message</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Challenge Modal -->
<div class="modal fade" id="assignChallengeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-puzzle me-2"></i>Assign Challenge
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignChallengeForm">
                    <input type="hidden" id="assignStudentId" value="">
                    <div class="mb-3">
                        <label for="challengeSelect" class="form-label">Select Challenge</label>
                        <select class="form-select" id="challengeSelect" required>
                            <option value="">Choose a challenge...</option>
                            {{-- TODO: Populate with available challenges --}}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignmentNote" class="form-label">Note (Optional)</label>
                        <textarea class="form-control" id="assignmentNote" rows="3" 
                                  placeholder="Add a personal note or guidance for this student..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="dueDate" class="form-label">Due Date (Optional)</label>
                        <input type="date" class="form-control" id="dueDate" min="{{ date('Y-m-d') }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignment()">Assign Challenge</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showQuickInsights(studentId) {
    const modal = new bootstrap.Modal(document.getElementById('quickInsightsModal'));
    const content = document.getElementById('quickInsightsContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading insights...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch student insights
    fetch(`/api/teacher/student/${studentId}/quick-insights`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5>${data.overall_performance || 0}%</h5>
                                <p class="mb-0">Overall Performance</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5>${data.success_rate || 0}%</h5>
                                <p class="mb-0">Success Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <h6>Learning Dimensions</h6>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Cognitive</span>
                                <span>${data.cognitive_score || 0}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: ${data.cognitive_score || 0}%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Motivation</span>
                                <span>${data.engagement_level || 0}%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: ${data.engagement_level || 0}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <h6>Recommendations:</h6>
                    <ul class="mb-0">
                        ${data.recommendations ? data.recommendations.map(rec => `<li>${rec}</li>`).join('') : '<li>Continue monitoring progress</li>'}
                    </ul>
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

function sendMessage(studentId) {
    document.getElementById('messageStudentId').value = studentId;
    const modal = new bootstrap.Modal(document.getElementById('sendMessageModal'));
    modal.show();
}

function submitMessage() {
    const studentId = document.getElementById('messageStudentId').value;
    const subject = document.getElementById('messageSubject').value;
    const content = document.getElementById('messageContent').value;
    
    if (!subject || !content) {
        alert('Please fill in both subject and message content.');
        return;
    }
    
    // Submit message
    fetch('/api/teacher/send-message', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            student_id: studentId,
            subject: subject,
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('sendMessageModal')).hide();
            alert('Message sent successfully!');
            // Reset form
            document.getElementById('messageForm').reset();
        } else {
            alert('Failed to send message: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error sending message: ' + error.message);
    });
}

function assignChallenge(studentId) {
    document.getElementById('assignStudentId').value = studentId;
    
    // Load available challenges
    loadAvailableChallenges();
    
    const modal = new bootstrap.Modal(document.getElementById('assignChallengeModal'));
    modal.show();
}

function loadAvailableChallenges() {
    const select = document.getElementById('challengeSelect');
    select.innerHTML = '<option value="">Loading challenges...</option>';
    
    fetch('/api/teacher/challenges/available')
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">Choose a challenge...</option>';
            data.challenges.forEach(challenge => {
                const option = document.createElement('option');
                option.value = challenge.id;
                option.textContent = `${challenge.title} (${challenge.difficulty})`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            select.innerHTML = '<option value="">Error loading challenges</option>';
        });
}

function submitAssignment() {
    const studentId = document.getElementById('assignStudentId').value;
    const challengeId = document.getElementById('challengeSelect').value;
    const note = document.getElementById('assignmentNote').value;
    const dueDate = document.getElementById('dueDate').value;
    
    if (!challengeId) {
        alert('Please select a challenge to assign.');
        return;
    }
    
    // Submit assignment
    fetch('/api/teacher/assign-challenge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            student_id: studentId,
            challenge_id: challengeId,
            note: note,
            due_date: dueDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('assignChallengeModal')).hide();
            alert('Challenge assigned successfully!');
            // Reset form
            document.getElementById('assignChallengeForm').reset();
        } else {
            alert('Failed to assign challenge: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error assigning challenge: ' + error.message);
    });
}

function resetProgress(studentId) {
    if (confirm('Are you sure you want to reset this student\'s progress? This action cannot be undone.')) {
        fetch(`/api/teacher/student/${studentId}/reset-progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Student progress has been reset.');
                location.reload(); // Refresh page to show updated data
            } else {
                alert('Failed to reset progress: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error resetting progress: ' + error.message);
        });
    }
}

function exportStudentData() {
    // Export student data as CSV
    window.location.href = '/api/teacher/export/students?format=csv';
}

// Auto-refresh data every 2 minutes
setInterval(function() {
    // Optionally refresh the page data
    // location.reload();
}, 120000);

/*
TODO: Advanced Features for Student Management

1. Bulk operations (assign challenges to multiple students)
2. Student grouping and cohort management
3. Performance trend analysis
4. Automated intervention triggers
5. Parent/guardian notifications
6. Integration with external gradebook systems
7. Advanced search and filtering
8. Student collaboration features
*/
</script>
@endpush