{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left side - Branding -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-primary text-white">
            <div class="text-center">
                <div class="mb-4">
                    <i class="bi bi-mortarboard-fill" style="font-size: 4rem;"></i>
                </div>
                <h1 class="fw-bold mb-3">{{ config('app.name', 'Learner Environment') }}</h1>
                <p class="lead mb-4">Adaptive Algorithmic Learning Platform</p>
                <div class="row text-center">
                    <div class="col">
                        <i class="bi bi-cpu-fill fs-1 mb-2"></i>
                        <h6>AI-Powered</h6>
                        <small>Intelligent code evaluation</small>
                    </div>
                    <div class="col">
                        <i class="bi bi-graph-up fs-1 mb-2"></i>
                        <h6>Adaptive</h6>
                        <small>Personalized learning paths</small>
                    </div>
                    <div class="col">
                        <i class="bi bi-people-fill fs-1 mb-2"></i>
                        <h6>Collaborative</h6>
                        <small>Teacher insights & clustering</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Login Form -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-light">
            <div class="w-100" style="max-width: 400px;">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Welcome Back</h3>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       placeholder="your.email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="Your password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <!-- Login Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>

                            <!-- Links -->
                            <div class="text-center">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link text-decoration-none" href="{{ route('password.request') }}">
                                        <i class="bi bi-question-circle me-1"></i>Forgot your password?
                                    </a>
                                @endif
                            </div>

                            <hr class="my-4">

                            <!-- Register Link -->
                            <div class="text-center">
                                <p class="mb-0">Don't have an account?</p>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-person-plus me-2"></i>Create Account
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Demo Accounts -->
                <div class="card mt-4 bg-info bg-opacity-10">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Demo Accounts
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Student</strong><br>
                                <small>alice@learner.com</small><br>
                                <small class="text-muted">password</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Teacher</strong><br>
                                <small>teacher1@learner.com</small><br>
                                <small class="text-muted">password</small>
                            </div>
                            <div class="col-md-4">
                                <strong>Admin</strong><br>
                                <small>admin@learner.com</small><br>
                                <small class="text-muted">password</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            password.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
@endsection