{{-- resources/views/challenges/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-puzzle me-2"></i>All Challenges
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <ul class="dropdown-menu">
                <li><h6 class="dropdown-header">By Difficulty</h6></li>
                <li><a class="dropdown-item" href="?difficulty=easy">Easy</a></li>
                <li><a class="dropdown-item" href="?difficulty=medium">Medium</a></li>
                <li><a class="dropdown-item" href="?difficulty=hard">Hard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">By Competency</h6></li>
                <li><a class="dropdown-item" href="?competency=algorithms">Algorithms</a></li>
                <li><a class="dropdown-item" href="?competency=data_structures">Data Structures</a></li>
                <li><a class="dropdown-item" href="?competency=problem_solving">Problem Solving</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('challenges.index') }}">Clear Filters</a></li>
            </ul>
        </div>
        
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleView('grid')" id="gridViewBtn">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="toggleView('list')" id="listViewBtn">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>
</div>

<!-- Filter Summary -->
@if(request()->hasAny(['difficulty', 'competency', 'search']))
<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-info-circle me-2"></i>
        Filters applied: 
        @if(request('difficulty'))
            <span class="badge bg-primary me-1">{{ ucfirst(request('difficulty')) }}</span>
        @endif
        @if(request('competency'))
            <span class="badge bg-secondary me-1">{{ ucfirst(str_replace('_', ' ', request('competency'))) }}</span>
        @endif
        @if(request('search'))
            <span class="badge bg-info me-1">Search: "{{ request('search') }}"</span>
        @endif
    </div>
    <a href="{{ route('challenges.index') }}" class="alert-link">Clear all filters</a>
</div>
@endif

<!-- Search Bar -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('challenges.index') }}" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search challenges by title, description, or competency...">
                </div>
            </div>
            <div class="col-md-2">
                <select name="difficulty" class="form-select">
                    <option value="">All Difficulties</option>
                    <option value="easy" {{ request('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                    <option value="medium" {{ request('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="hard" {{ request('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Challenges Display -->
<div id="challengesContainer">
    <!-- Grid View (Default Hidden) -->
    <div id="gridView" class="row" style="display: none;">
        @forelse($challenges as $challenge)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card challenge-card shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-{{ $challenge->difficulty === 'easy' ? 'success' : ($challenge->difficulty === 'medium' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($challenge->difficulty) }}
                                </span>
                                <h6 class="card-title mt-2 mb-1">{{ $challenge->title }}</h6>
                                <small class="text-muted">{{ $challenge->competency->name }}</small>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-primary">{{ $challenge->points }}pt</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body pt-0">
                        <p class="card-text text-muted">
                            {{ Str::limit($challenge->description, 100) }}
                        </p>
                        
                        <div class="row text-center small text-muted">
                            <div class="col-4">
                                <div>Time Limit</div>
                                <strong>{{ $challenge->time_limit }}min</strong>
                            </div>
                            <div class="col-4">
                                <div>Max Attempts</div>
                                <strong>{{ $challenge->max_attempts }}</strong>
                            </div>
                            <div class="col-4">
                                <div>Attempts</div>
                                <strong>{{ $challenge->attempts_count ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white">
                        <div class="d-grid">
                            @if(auth()->check() && auth()->user()->isStudent())
                                <a href="{{ route('student.challenge', $challenge) }}" class="btn btn-primary">
                                    <i class="bi bi-play-circle me-1"></i>Start Challenge
                                </a>
                            @else
                                <a href="{{ route('challenges.show', $challenge) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No challenges found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- List View (Default Shown) -->
    <div id="listView" class="card shadow">
        <div class="card-body">
            @forelse($challenges as $challenge)
                <div class="challenge-list-item d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <!-- Challenge Icon & Difficulty -->
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-{{ $challenge->difficulty === 'easy' ? 'success' : ($challenge->difficulty === 'medium' ? 'warning' : 'danger') }} bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-puzzle fs-4 text-{{ $challenge->difficulty === 'easy' ? 'success' : ($challenge->difficulty === 'medium' ? 'warning' : 'danger') }}"></i>
                        </div>
                    </div>
                    
                    <!-- Challenge Details -->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ auth()->check() && auth()->user()->isStudent() ? route('student.challenge', $challenge) : route('challenges.show', $challenge) }}" 
                                       class="text-decoration-none">
                                        {{ $challenge->title }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-1">{{ $challenge->description }}</p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-{{ $challenge->difficulty === 'easy' ? 'success' : ($challenge->difficulty === 'medium' ? 'warning' : 'danger') }} me-2">
                                        {{ ucfirst($challenge->difficulty) }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="bi bi-tag me-1"></i>{{ $challenge->competency->name }}
                                    </small>
                                    <small class="text-muted ms-3">
                                        <i class="bi bi-clock me-1"></i>{{ $challenge->time_limit }} min
                                    </small>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <div class="badge bg-primary mb-1">{{ $challenge->points }} points</div>
                                <div class="small text-muted">{{ $challenge->attempts_count ?? 0 }} attempts</div>
                            </div>
                        </div>
                        
                        <!-- User Progress (for authenticated students) -->
                        @if(auth()->check() && auth()->user()->isStudent())
                            @php
                                $userAttempts = auth()->user()->attempts->where('challenge_id', $challenge->id);
                                $bestScore = $userAttempts->max('score') ?? 0;
                                $hasCompleted = $userAttempts->where('is_successful', true)->count() > 0;
                            @endphp
                            
                            @if($userAttempts->count() > 0)
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar {{ $hasCompleted ? 'bg-success' : ($bestScore >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                         role="progressbar" 
                                         style="width: {{ $bestScore }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Your best: {{ $bestScore }}%</small>
                                    @if($hasCompleted)
                                        <small class="text-success"><i class="bi bi-check-circle me-1"></i>Completed</small>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex-shrink-0 ms-3">
                        <div class="btn-group">
                            @if(auth()->check() && auth()->user()->isStudent())
                                <a href="{{ route('student.challenge', $challenge) }}" 
                                   class="btn btn-primary">
                                    @if($userAttempts->where('is_successful', true)->count() > 0)
                                        <i class="bi bi-check-circle me-1"></i>Review
                                    @elseif($userAttempts->count() > 0)
                                        <i class="bi bi-arrow-right-circle me-1"></i>Continue
                                    @else
                                        <i class="bi bi-play-circle me-1"></i>Start
                                    @endif
                                </a>
                            @else
                                <a href="{{ route('challenges.show', $challenge) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No challenges found</h4>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                    @if(auth()->check() && auth()->user()->isTeacher())
                        <a href="{{ route('teacher.challenge.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Create New Challenge
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Pagination -->
@if($challenges->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $challenges->appends(request()->query())->links() }}
    </div>
@endif

<!-- Statistics Summary -->
@if($challenges->count() > 0)
<div class="card shadow mt-4">
    <div class="card-body">
        <h6 class="card-title">
            <i class="bi bi-bar-chart me-2"></i>Challenge Statistics
        </h6>
        <div class="row text-center">
            <div class="col-md-3">
                <div class="text-muted small">Total Challenges</div>
                <div class="h5 mb-0 font-weight-bold text-primary">{{ $challenges->total() }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Easy</div>
                <div class="h5 mb-0 font-weight-bold text-success">
                    {{ $challenges->where('difficulty', 'easy')->count() }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Medium</div>
                <div class="h5 mb-0 font-weight-bold text-warning">
                    {{ $challenges->where('difficulty', 'medium')->count() }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Hard</div>
                <div class="h5 mb-0 font-weight-bold text-danger">
                    {{ $challenges->where('difficulty', 'hard')->count() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function toggleView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (viewType === 'grid') {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('challengeView', 'grid');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('challengeView', 'list');
    }
}

// Restore saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('challengeView') || 'list';
    toggleView(savedView);
    
    // Setup search auto-submit on Enter
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
});

// Enhanced challenge card interactions
document.querySelectorAll('.challenge-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.transition = 'transform 0.2s ease-in-out';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
});

// Infinite scroll for large challenge lists (optional enhancement)
let isLoading = false;
function setupInfiniteScroll() {
    window.addEventListener('scroll', function() {
        if (isLoading) return;
        
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
            loadMoreChallenges();
        }
    });
}

function loadMoreChallenges() {
    // Implementation for loading more challenges via AJAX
    // This would be useful for sites with hundreds of challenges
    isLoading = true;
    
    const nextPageUrl = document.querySelector('.pagination .page-item:last-child a')?.href;
    if (nextPageUrl && nextPageUrl !== window.location.href) {
        fetch(nextPageUrl)
            .then(response => response.text())
            .then(html => {
                // Parse and append new challenges
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChallenges = doc.querySelectorAll('.challenge-card, .challenge-list-item');
                
                if (newChallenges.length > 0) {
                    const container = document.querySelector('#challengesContainer .row, #listView .card-body');
                    newChallenges.forEach(challenge => {
                        container.appendChild(challenge);
                    });
                }
                
                isLoading = false;
            })
            .catch(error => {
                console.error('Error loading more challenges:', error);
                isLoading = false;
            });
    } else {
        isLoading = false;
    }
}

// Optional: Setup infinite scroll for better UX
// setupInfiniteScroll();

/*
TODO: Enhanced Features for Challenge Index

1. Advanced search with autocomplete
2. Bookmark/favorite challenges for students
3. Challenge difficulty calculator based on user performance
4. Related challenges recommendations
5. Challenge completion progress visualization
6. Social features (comments, ratings)
7. Challenge categories and tags
8. Personalized challenge recommendations
9. Challenge completion certificates
10. Leaderboards per challenge
*/
</script>
@endpush