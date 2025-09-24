{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2 me-2"></i>System Administration
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-person-plus me-1"></i>New User
            </a>
            <a href="{{ route('admin.competency.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i>New Competency
            </a>
        </div>
    </div>
</div>

<!-- System Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        <div class="text-xs text-muted">
                            {{ $stats['students'] }} students, {{ $stats['teachers'] }} teachers
                        </div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Challenges</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['challenges'] }}</div>
                        <div class="text-xs text-muted">
                            {{ $stats['competencies'] }} competencies
                        </div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_attempts']) }}</div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">System Status</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-success">Online</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-shield-check fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- System Services Status -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-server me-2"></i>System Services Status
                </h6>
            </div>
            <div class="card-body">
                <div class="service-status-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-success rounded-circle me-3"></div>
                            <div>
                                <div class="fw-bold">Laravel Backend</div>
                                <small class="text-muted">Application server running</small>
                            </div>
                        </div>
                        <span class="badge bg-success">Online</span>
                    </div>
                </div>

                <div class="service-status-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-success rounded-circle me-3" id="pythonStatus"></div>
                            <div>
                                <div class="fw-bold">Python AI Services</div>
                                <small class="text-muted">FastAPI microservice</small>
                            </div>
                        </div>
                        <span class="badge bg-success" id="pythonStatusBadge">Checking...</span>
                    </div>
                </div>

                <div class="service-status-item mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-success rounded-circle me-3"></div>
                            <div>
                                <div class="fw-bold">MySQL Database</div>
                                <small class="text-muted">Data persistence layer</small>
                            </div>
                        </div>
                        <span class="badge bg-success">Online</span>
                    </div>
                </div>

                <div class="service-status-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-warning rounded-circle me-3"></div>
                            <div>
                                <div class="fw-bold">Redis Cache</div>
                                <small class="text-muted">Session & queue management</small>
                            </div>
                        </div>
                        <span class="badge bg-warning">Optional</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person-plus me-2"></i>Recent Users
                </h6>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                    @foreach($recentUsers as $user)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'teacher' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                <div class="text-muted small">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted">
                        <i class="bi bi-people fs-3 mb-2"></i>
                        <p>No recent user registrations</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Management Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-tools me-2"></i>System Management
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 text-primary mb-3"></i>
                                <h6>User Management</h6>
                                <p class="text-muted small">Manage users, roles, and permissions</p>
                                <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-right me-1"></i>Manage Users
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-award fs-1 text-success mb-3"></i>
                                <h6>Competencies</h6>
                                <p class="text-muted small">Define learning competencies and skills</p>
                                <a href="{{ route('admin.competencies') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-arrow-right me-1"></i>Manage Competencies
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="bi bi-gear fs-1 text-warning mb-3"></i>
                                <h6>System Settings</h6>
                                <p class="text-muted small">Configure application settings</p>
                                <a href="{{ route('admin.settings') }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-arrow-right me-1"></i>System Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-3">
                        <button class="btn btn-outline-info w-100" onclick="checkSystemHealth()">
                            <i class="bi bi-shield-check me-2"></i>Health Check
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-warning w-100" onclick="clearCache()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Clear Cache
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100" onclick="exportSystemData()">
                            <i class="bi bi-download me-2"></i>Export Data
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-danger w-100" onclick="viewSystemLogs()">
                            <i class="bi bi-journal-text me-2"></i>System Logs
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.status-indicator {
    width: 12px;
    height: 12px;
    flex-shrink: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check Python service status
    checkPythonServiceStatus();
    
    // Set up auto-refresh for system status
    setInterval(checkPythonServiceStatus, 30000); // Every 30 seconds
});

function checkPythonServiceStatus() {
    const statusIndicator = document.getElementById('pythonStatus');
    const statusBadge = document.getElementById('pythonStatusBadge');
    
    fetch('/api/health/python-service')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'healthy') {
                statusIndicator.className = 'status-indicator bg-success rounded-circle me-3';
                statusBadge.className = 'badge bg-success';
                statusBadge.textContent = 'Online';
            } else {
                statusIndicator.className = 'status-indicator bg-danger rounded-circle me-3';
                statusBadge.className = 'badge bg-danger';
                statusBadge.textContent = 'Offline';
            }
        })
        .catch(error => {
            statusIndicator.className = 'status-indicator bg-warning rounded-circle me-3';
            statusBadge.className = 'badge bg-warning';
            statusBadge.textContent = 'Unknown';
        });
}

function checkSystemHealth() {
    // Perform comprehensive system health check
    fetch('/api/admin/health-check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        let message = 'System Health Check Results:\n\n';
        Object.keys(data).forEach(service => {
            message += `${service}: ${data[service].status}\n`;
        });
        alert(message);
    })
    .catch(error => {
        alert('Failed to perform health check: ' + error.message);
    });
}

function clearCache() {
    if (confirm('Are you sure you want to clear the application cache?')) {
        fetch('/api/admin/clear-cache', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache cleared successfully!');
            } else {
                alert('Failed to clear cache: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error clearing cache: ' + error.message);
        });
    }
}

function exportSystemData() {
    // Export system data for backup/analysis
    window.location.href = '/api/admin/export/system-data?format=json';
}

function viewSystemLogs() {
    // Open system logs in new window
    window.open('/api/admin/logs', '_blank');
}

/*
TODO: Advanced Admin Features

1. Real-time system monitoring dashboard
2. User activity analytics
3. Performance metrics and alerts
4. Automated backup scheduling
5. Security audit logs
6. Database query optimization tools
7. Feature flag management
8. A/B testing configuration
9. Email notification settings
10. API rate limiting controls
*/
</script>
@endpush