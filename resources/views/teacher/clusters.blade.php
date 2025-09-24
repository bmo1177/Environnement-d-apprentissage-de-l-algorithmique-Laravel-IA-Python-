@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-diagram-3 me-2"></i>Student Clusters
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-primary" id="refreshClustering">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh Clusters
            </button>
        </div>
    </div>

    <!-- Clustering Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Clustering Overview</h6>
                </div>
                <div class="card-body">
                    @if(empty($clusters))
                        <div class="text-center py-5">
                            <i class="bi bi-diagram-3 fs-1 text-muted mb-3"></i>
                            <h5>No clustering data available</h5>
                            <p class="text-muted">Run the clustering algorithm to group students based on learning patterns.</p>
                            <button class="btn btn-primary mt-2" id="runClustering">
                                <i class="bi bi-play-circle me-1"></i>Run Clustering
                            </button>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center mb-4">
                                    <canvas id="clusterDistributionChart" height="250"></canvas>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5>Cluster Analysis</h5>
                                <p>The system has identified {{ count($clusters) }} distinct learning pattern groups among your students.</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cluster</th>
                                                <th>Size</th>
                                                <th>Key Characteristics</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($clusters as $index => $cluster)
                                                <tr>
                                                    <td>Cluster {{ $index + 1 }}</td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $cluster['size'] ?? 0 }} students</span>
                                                    </td>
                                                    <td>
                                                        @if(isset($cluster['characteristics']))
                                                            @foreach($cluster['characteristics'] as $key => $value)
                                                                <div><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No characteristics data</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary view-cluster" data-cluster-id="{{ $index }}">
                                                            <i class="bi bi-eye me-1"></i>View
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Cluster View -->
    @if(!empty($clusters))
        @foreach($clusters as $index => $cluster)
            <div class="row mb-4 cluster-detail" id="cluster-{{ $index }}" style="display: none;">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Cluster {{ $index + 1 }} Details</h6>
                            <button class="btn btn-sm btn-outline-secondary close-cluster">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">Characteristics</div>
                                        <div class="card-body">
                                            @if(isset($cluster['characteristics']))
                                                @foreach($cluster['characteristics'] as $key => $value)
                                                    <div class="mb-2">
                                                        <h6>{{ ucfirst($key) }}</h6>
                                                        <p>{{ $value }}</p>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">No characteristics data available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">Recommended Actions</div>
                                        <div class="card-body">
                                            @if(isset($cluster['characteristics']))
                                                @php
                                                    $recommendations = [];
                                                    $characteristics = $cluster['characteristics'];
                                                    
                                                    // Generate recommendations based on characteristics
                                                    if (isset($characteristics['cognitive']) && str_contains($characteristics['cognitive'], 'Needs')) {
                                                        $recommendations[] = 'Provide foundational learning materials and basic concept reviews';
                                                    }
                                                    
                                                    if (isset($characteristics['behavioral']) && str_contains($characteristics['behavioral'], 'Irregular')) {
                                                        $recommendations[] = 'Set up regular check-ins and engagement activities';
                                                    }
                                                    
                                                    if (isset($characteristics['motivational']) && str_contains($characteristics['motivational'], 'Low')) {
                                                        $recommendations[] = 'Implement gamification elements and provide more immediate feedback';
                                                    }
                                                    
                                                    // Add default recommendations if none were generated
                                                    if (empty($recommendations)) {
                                                        if (isset($characteristics['cognitive']) && str_contains($characteristics['cognitive'], 'High')) {
                                                            $recommendations[] = 'Provide advanced challenges to maintain engagement';
                                                        } else {
                                                            $recommendations[] = 'Regular progress monitoring and personalized feedback';
                                                        }
                                                    }
                                                @endphp
                                                
                                                <ul class="list-group">
                                                    @foreach($recommendations as $recommendation)
                                                        <li class="list-group-item">
                                                            <i class="bi bi-lightbulb me-2 text-warning"></i>
                                                            {{ $recommendation }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted">No recommendations available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Students in this cluster</h5>
                                    <p class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Click on a student to view their detailed profile
                                    </p>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student ID</th>
                                                    <th>Name</th>
                                                    <th>Progress</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($cluster['students']) && count($cluster['students']) > 0)
                                                    @foreach($cluster['students'] as $student)
                                                        <tr>
                                                            <td>{{ $student['id'] ?? 'N/A' }}</td>
                                                            <td>{{ $student['name'] ?? 'Unknown' }}</td>
                                                            <td>
                                                                <div class="progress">
                                                                    <div class="progress-bar" role="progressbar" 
                                                                        style="width: {{ $student['progress'] ?? 50 }}%;" 
                                                                        aria-valuenow="{{ $student['progress'] ?? 50 }}" 
                                                                        aria-valuemin="0" 
                                                                        aria-valuemax="100">
                                                                        {{ $student['progress'] ?? 50 }}%
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('teacher.studentDetail', ['id' => $student['id'] ?? 1]) }}" 
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-person me-1"></i>Profile
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted py-3">
                                                            Student details not available
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Running Clustering Analysis</h5>
                <p class="text-muted mb-0">This may take a few moments...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts if clusters exist
        @if(!empty($clusters))
            initializeClusterChart();
        @endif
        
        // Run clustering button
        document.querySelectorAll('#runClustering, #refreshClustering').forEach(button => {
            button.addEventListener('click', function() {
                const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                loadingModal.show();
                
                // Call the clustering endpoint
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
                    loadingModal.hide();
                    if (data.success) {
                        // Reload the page to show new clusters
                        window.location.reload();
                    } else {
                        alert('Clustering failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    loadingModal.hide();
                    alert('Error running clustering: ' + error);
                });
            });
        });
        
        // View cluster details
        document.querySelectorAll('.view-cluster').forEach(button => {
            button.addEventListener('click', function() {
                const clusterId = this.getAttribute('data-cluster-id');
                
                // Hide all cluster details
                document.querySelectorAll('.cluster-detail').forEach(el => {
                    el.style.display = 'none';
                });
                
                // Show selected cluster
                document.getElementById('cluster-' + clusterId).style.display = 'flex';
                
                // Scroll to the details
                document.getElementById('cluster-' + clusterId).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Close cluster details
        document.querySelectorAll('.close-cluster').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.cluster-detail').style.display = 'none';
            });
        });
    });
    
    function initializeClusterChart() {
        const ctx = document.getElementById('clusterDistributionChart').getContext('2d');
        
        // Extract cluster data
        const clusterLabels = [];
        const clusterSizes = [];
        const backgroundColors = [
            'rgba(78, 115, 223, 0.8)',
            'rgba(28, 200, 138, 0.8)',
            'rgba(246, 194, 62, 0.8)',
            'rgba(231, 74, 59, 0.8)',
            'rgba(54, 185, 204, 0.8)',
            'rgba(133, 135, 150, 0.8)'
        ];
        
        @foreach($clusters as $index => $cluster)
            clusterLabels.push('Cluster {{ $index + 1 }}');
            clusterSizes.push({{ $cluster['size'] ?? 0 }});
        @endforeach
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: clusterLabels,
                datasets: [{
                    data: clusterSizes,
                    backgroundColor: backgroundColors.slice(0, clusterLabels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Student Distribution'
                    }
                },
                cutout: '70%'
            }
        });
    }
</script>
@endsection
