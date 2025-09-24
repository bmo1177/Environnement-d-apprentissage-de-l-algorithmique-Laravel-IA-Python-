{{-- resources/views/layouts/partials/sidebar.blade.php --}}
<div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky pt-3">
        @auth
            <!-- User Info Card -->
            <div class="card bg-light mb-3">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <h6 class="card-title mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ ucfirst(auth()->user()->role) }}</small>
                    @if(auth()->user()->student_id)
                        <div class="badge bg-secondary mt-1">{{ auth()->user()->student_id }}</div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isStudent())
                <!-- Student Navigation -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                           href="{{ route('student.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.challenges') ? 'active' : '' }}" 
                           href="{{ route('student.challenges') }}">
                            <i class="bi bi-puzzle me-2"></i>Challenges
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" 
                           href="{{ route('student.profile') }}">
                            <i class="bi bi-person me-2"></i>My Profile
                        </a>
                    </li>
                </ul>

                <hr>

                <!-- Quick Stats for Students -->
                @if(auth()->user()->learnerProfile)
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                        <span>Quick Stats</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <div class="px-3 py-2">
                                <div class="d-flex justify-content-between">
                                    <span>Total Attempts:</span>
                                    <span class="badge bg-primary">{{ auth()->user()->learnerProfile->total_attempts ?? 0 }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <div class="px-3 py-2">
                                <div class="d-flex justify-content-between">
                                    <span>Success Rate:</span>
                                    @php
                                        $profile = auth()->user()->learnerProfile;
                                        $rate = $profile && $profile->total_attempts > 0 
                                            ? round(($profile->successful_attempts / $profile->total_attempts) * 100)
                                            : 0;
                                    @endphp
                                    <span class="badge {{ $rate >= 70 ? 'bg-success' : ($rate >= 40 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $rate }}%
                                    </span>
                                </div>
                            </div>
                        </li>
                        @if($profile && $profile->streak_days > 0)
                            <li class="nav-item">
                                <div class="px-3 py-2">
                                    <div class="d-flex justify-content-between">
                                        <span>Current Streak:</span>
                                        <span class="badge streak-badge text-white">{{ $profile->streak_days }} days</span>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                @endif

            @elseif(auth()->user()->isTeacher())
                <!-- Teacher Navigation -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" 
                           href="{{ route('teacher.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.students') ? 'active' : '' }}" 
                           href="{{ route('teacher.students') }}">
                            <i class="bi bi-people me-2"></i>Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.challenges') ? 'active' : '' }}" 
                           href="{{ route('teacher.challenges') }}">
                            <i class="bi bi-puzzle me-2"></i>Manage Challenges
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.clusters') ? 'active' : '' }}" 
                           href="{{ route('teacher.clusters') }}">
                            <i class="bi bi-diagram-3 me-2"></i>Student Clusters
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.analytics') ? 'active' : '' }}" 
                           href="{{ route('teacher.analytics') }}">
                            <i class="bi bi-graph-up me-2"></i>Analytics
                        </a>
                    </li>
                </ul>

                <hr>

                <!-- Quick Actions for Teachers -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Quick Actions</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.challenge.create') }}">
                            <i class="bi bi-plus-circle me-2"></i>New Challenge
                        </a>
                    </li>
                </ul>

            @elseif(auth()->user()->isAdmin())
                <!-- Admin Navigation -->
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" 
                           href="{{ route('admin.users') }}">
                            <i class="bi bi-people me-2"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.competencies*') ? 'active' : '' }}" 
                           href="{{ route('admin.competencies') }}">
                            <i class="bi bi-award me-2"></i>Competencies
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" 
                           href="{{ route('admin.settings') }}">
                            <i class="bi bi-gear me-2"></i>System Settings
                        </a>
                    </li>
                </ul>

                <hr>

                <!-- Quick Actions for Admins -->
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
                    <span>Quick Actions</span>
                </h6>
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.user.create') }}">
                            <i class="bi bi-person-plus me-2"></i>New User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.competency.create') }}">
                            <i class="bi bi-plus-circle me-2"></i>New Competency
                        </a>
                    </li>
                </ul>
            @endif

        @else
            <!-- Guest sidebar content -->
            <div class="text-center p-4">
                <h5>Welcome to Learner Environment</h5>
                <p class="text-muted">Please log in to access your personalized learning dashboard.</p>
                <a href="{{ route('login') }}" class="btn btn-primary mb-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
            </div>
        @endauth
    </div>
</div>