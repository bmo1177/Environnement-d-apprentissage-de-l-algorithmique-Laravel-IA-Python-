{{-- resources/views/layouts/partials/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="bi bi-mortarboard-fill me-2"></i>
            {{ config('app.name', 'Laravel Learner') }}
        </a>

        <!-- Mobile menu toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left side navigation -->
            <ul class="navbar-nav me-auto">
                @auth
                    @if(auth()->user()->isStudent())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.challenges') ? 'active' : '' }}" 
                               href="{{ route('student.challenges') }}">
                                <i class="bi bi-puzzle me-1"></i>Challenges
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" 
                               href="{{ route('student.profile') }}">
                                <i class="bi bi-person me-1"></i>My Profile
                            </a>
                        </li>
                    @elseif(auth()->user()->isTeacher())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.students') ? 'active' : '' }}" 
                               href="{{ route('teacher.students') }}">
                                <i class="bi bi-people me-1"></i>Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.challenges') ? 'active' : '' }}" 
                               href="{{ route('teacher.challenges') }}">
                                <i class="bi bi-puzzle me-1"></i>Challenges
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('teacher.analytics') ? 'active' : '' }}" 
                               href="{{ route('teacher.analytics') }}">
                                <i class="bi bi-graph-up me-1"></i>Analytics
                            </a>
                        </li>
                    @elseif(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" 
                               href="{{ route('admin.users') }}">
                                <i class="bi bi-people me-1"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.competencies') ? 'active' : '' }}" 
                               href="{{ route('admin.competencies') }}">
                                <i class="bi bi-award me-1"></i>Competencies
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right side navigation -->
            <ul class="navbar-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                @else
                    <!-- User dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                           id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="bg-white text-primary rounded-circle p-1 me-2" style="width: 32px; height: 32px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            {{ auth()->user()->name }}
                            <span class="badge bg-light text-primary ms-2">{{ ucfirst(auth()->user()->role) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="bi bi-person-circle me-2"></i>{{ auth()->user()->name }}
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            @if(auth()->user()->isStudent())
                                <li>
                                    <a class="dropdown-item" href="{{ route('student.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('student.profile') }}">
                                        <i class="bi bi-person me-2"></i>My Profile
                                    </a>
                                </li>
                            @elseif(auth()->user()->isTeacher())
                                <li>
                                    <a class="dropdown-item" href="{{ route('teacher.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('teacher.clusters') }}">
                                        <i class="bi bi-diagram-3 me-2"></i>Student Clusters
                                    </a>
                                </li>
                            @elseif(auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                        <i class="bi bi-gear me-2"></i>Settings
                                    </a>
                                </li>
                            @endif
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>