{{-- resources/views/student/challenges.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-puzzle me-2"></i>Available Challenges
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?difficulty=easy">Easy</a></li>
                <li><a class="dropdown-item" href="?difficulty=medium">Medium</a></li>
                <li><a class="dropdown-item" href="?difficulty=hard">Hard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('student.challenges') }}">All</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Summary -->
@if(request('difficulty'))
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Showing <strong>{{ ucfirst(request('difficulty')) }}</strong> challenges only.
    <a href="{{ route('student.challenges') }}" class="alert-link">Show all</a>
</div>
@endif

<!-- Challenges Grid -->
<div class="row">
    @forelse($challenges as $challenge)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card challenge-card shadow-sm h-100">
                <!-- Challenge Header -->
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-{{ $challenge->difficulty === 'easy' ? 'success' : ($challenge->difficulty === 'medium' ? 'warning' : 'danger') }} mb-2">
                                <i class="bi bi-{{ $challenge->difficulty === 'easy' ? 'circle' : ($challenge->difficulty === 'medium' ? 'diamond' : 'square') }}-fill me-1"></i>
                                {{ ucfirst($challenge->difficulty) }}
                            </span>
                            <h5 class="card-title mb-1">{{ $challenge->title }}</h5>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-tag me-1"></i>{{ $challenge->competency->name }}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-primary">{{ $challenge->points }}pt</div>
                        </div>
                    </div>
                </div>

                <!-- Challenge Content -->
                <div class="card-body pt-2">
                    <p class="card-text text-muted">
                        {{ Str::limit($challenge->description, 120) }}
                    </p>

                    <!-- Challenge Stats -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="text-muted small">Attempts</div>
                            <div class="fw-bold">{{ $challenge->attempts_count ?? 0 }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Time Limit</div>
                            <div class="fw-bold">{{ $challenge->time_limit }}min</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Max Tries</div>
                            <div class="fw-bold">{{ $challenge->max_attempts }}</div>
                        </div>
                    </div>

                    <!-- User's Progress -->
                    @php
                        $userAttempts = auth()->user()->attempts->where('challenge_id', $challenge->id);
                        $bestScore = $userAttempts->max('score') ?? 0;
                        $attemptCount = $userAttempts->count();
                        $hasCompleted = $userAttempts->where('is_successful', true)->count() > 0;
                    @endphp

                    @if($attemptCount > 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Your best: {{ $bestScore }}%</small>
                                <small class="text-muted">{{ $attemptCount }}/{{ $challenge->max_attempts }} attempts</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $hasCompleted ? 'bg-success' : ($bestScore >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" style="width: {{ $bestScore }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Challenge Actions -->
                <div class="card-footer bg-white border-0">
                    <div class="d-grid gap-2">
                        @if($attemptCount >= $challenge->max_attempts && !$hasCompleted)
                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-x-circle me-2"></i>Max Attempts Reached
                            </button>
                        @else
                            <a href="{{ route('student.challenge', $challenge) }}" class="btn btn-primary">
                                @if($hasCompleted)
                                    <i class="bi bi-check-circle me-2"></i>Review Solution
                                @elseif($attemptCount > 0)
                                    <i class="bi bi-arrow-right-circle me-2"></i>Continue Challenge
                                @else
                                    <i class="bi bi-play-circle me-2"></i>Start Challenge
                                @endif
                            </a>
                        @endif

                        @if($attemptCount > 0)
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="showAttemptHistory({{ $challenge->id }})">
                                            <i class="bi bi-clock-history me-2"></i>View History
                                        </a>
                                    </li>
                                    @if($userAttempts->whereNotNull('ai_feedback')->count() > 0)
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="showAiFeedback({{ $challenge->id }})">
                                                <i class="bi bi-robot me-2"></i>AI Feedback
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>

                    @if($hasCompleted)
                        <div class="text-center mt-2">
                            <small class="text-success">
                                <i class="bi bi-trophy-fill me-1"></i>Completed
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Every 3 cards, add a break for better mobile layout -->
        @if($loop->iteration % 3 == 0)
            <div class="w-100 d-none d-lg-block"></div>
        @endif

    @empty
        <!-- No Challenges State -->
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                <h4 class="text-muted">No challenges available</h4>
                <p class="text-muted">Check back later for new challenges!</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($challenges->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $challenges->appends(request()->query())->links() }}
    </div>
@endif

<!-- Modals -->
<!-- Attempt History Modal -->
<div class="modal fade" id="attemptHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-2"></i>Attempt History
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attemptHistoryContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
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
                    <i class="bi bi-robot me-2"></i>AI Feedback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="aiFeedbackContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAttemptHistory(challengeId) {
    const modal = new bootstrap.Modal(document.getElementById('attemptHistoryModal'));
    const content = document.getElementById('attemptHistoryContent');
    
    // Reset content
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch attempt history (placeholder - implement actual API call)
    fetch(`/api/student/attempts/${challengeId}`)
        .then(response => response.json())
        .then(data => {
            // Render attempt history
            let html = '<div class="timeline">';
            data.attempts.forEach(attempt => {
                html += `
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="badge ${attempt.is_successful ? 'bg-success' : 'bg-danger'} rounded-pill">
                                    ${attempt.score}%
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">Attempt #${attempt.attempt_number}</h6>
                                    <small class="text-muted">${attempt.created_at}</small>
                                </div>
                                <p class="text-muted mb-1">Time spent: ${attempt.time_spent || 'N/A'}</p>
                                ${attempt.error_message ? `<div class="alert alert-danger py-1">${attempt.error_message}</div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Failed to load attempt history.
                </div>
            `;
        });
}

function showAiFeedback(challengeId) {
    const modal = new bootstrap.Modal(document.getElementById('aiFeedbackModal'));
    const content = document.getElementById('aiFeedbackContent');
    
    // Reset content
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch AI feedback (placeholder - implement actual API call to FastAPI /recommend endpoint)
    fetch(`/api/student/feedback/${challengeId}`)
        .then(response => response.json())
        .then(data => {
            let html = '';
            
            if (data.feedback) {
                html += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightbulb me-2"></i>Primary Feedback
                            </h6>
                        </div>
                        <div class="card-body">
                            <p>${data.feedback.message}</p>
                        </div>
                    </div>
                `;
            }
            
            if (data.hints && data.hints.length > 0) {
                html += `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-question-circle me-2"></i>Hints
                            </h6>
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
            
            if (data.resources && data.resources.length > 0) {
                html += `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-book me-2"></i>Recommended Resources
                            </h6>
                        </div>
                        <div class="card-body">
                `;
                data.resources.forEach(resource => {
                    html += `
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-link-45deg me-2"></i>
                            <a href="${resource.url}" target="_blank" class="text-decoration-none">
                                ${resource.title} <small class="text-muted">(${resource.type})</small>
                            </a>
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
                    <p>No AI feedback available for this challenge yet.</p>
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

// Filter challenges by difficulty
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
    
    // Example: Auto-refresh for real-time updates (optional)
    /*
    setInterval(function() {
        // Refresh challenge completion status
        // This would call an API endpoint to get updated challenge data
    }, 30000); // Every 30 seconds
    */
});
</script>
@endpush