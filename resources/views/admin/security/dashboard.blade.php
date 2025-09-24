@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-shield-check me-2"></i>Security Dashboard
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-sm btn-primary" id="runSecurityScan">
                <i class="bi bi-search me-1"></i>Run Security Scan
            </button>
        </div>
    </div>

    <!-- Security Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Last Scan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lastScanDate">Never</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Files Scanned</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="filesScanCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-code fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Vulnerabilities</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="vulnerabilityCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Critical Issues</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="criticalCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bug fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Results -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Security Scan Results</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Export Options:</div>
                            <a class="dropdown-item" href="#" id="exportCSV">Export as CSV</a>
                            <a class="dropdown-item" href="#" id="exportPDF">Export as PDF</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="clearResults">Clear Results</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="scanningMessage" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Scanning...</span>
                        </div>
                        <h5>Security Scan in Progress</h5>
                        <p class="text-muted">This may take a few moments...</p>
                    </div>
                    
                    <div id="noResultsMessage" class="text-center py-5">
                        <i class="bi bi-shield-check fs-1 text-muted mb-3"></i>
                        <h5>No Scan Results Available</h5>
                        <p class="text-muted">Run a security scan to check your application for vulnerabilities.</p>
                    </div>
                    
                    <div id="scanResultsContainer" style="display: none;">
                        <!-- Vulnerability Summary -->
                        <div class="mb-4">
                            <h5>Vulnerability Summary</h5>
                            <div class="chart-container" style="position: relative; height:200px;">
                                <canvas id="vulnerabilityChart"></canvas>
                            </div>
                        </div>
                        
                        <!-- Vulnerability Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="vulnerabilityTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Line</th>
                                        <th>Type</th>
                                        <th>Severity</th>
                                        <th>Description</th>
                                        <th>Code</th>
                                    </tr>
                                </thead>
                                <tbody id="vulnerabilityTableBody">
                                    <!-- Results will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const runScanButton = document.getElementById('runSecurityScan');
        const scanningMessage = document.getElementById('scanningMessage');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const scanResultsContainer = document.getElementById('scanResultsContainer');
        
        let vulnerabilityChart = null;
        
        runScanButton.addEventListener('click', function() {
            // Show scanning message
            scanningMessage.style.display = 'block';
            noResultsMessage.style.display = 'none';
            scanResultsContainer.style.display = 'none';
            
            // Disable scan button
            runScanButton.disabled = true;
            
            // Call the security scan endpoint
            fetch('/admin/security/run-scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide scanning message
                scanningMessage.style.display = 'none';
                
                // Enable scan button
                runScanButton.disabled = false;
                
                if (data.success) {
                    // Update dashboard stats
                    document.getElementById('lastScanDate').textContent = new Date().toLocaleString();
                    document.getElementById('filesScanCount').textContent = data.report.summary.files_scanned;
                    document.getElementById('vulnerabilityCount').textContent = data.report.summary.vulnerabilities_found;
                    document.getElementById('criticalCount').textContent = data.report.summary.critical;
                    
                    // Show results container
                    scanResultsContainer.style.display = 'block';
                    
                    // Populate vulnerability table
                    populateVulnerabilityTable(data.report);
                    
                    // Create or update chart
                    createVulnerabilityChart(data.report);
                } else {
                    // Show error message
                    noResultsMessage.innerHTML = `
                        <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
                        <h5>Scan Failed</h5>
                        <p class="text-muted">${data.message || 'An error occurred during the security scan.'}</p>
                    `;
                    noResultsMessage.style.display = 'block';
                }
            })
            .catch(error => {
                // Hide scanning message
                scanningMessage.style.display = 'none';
                
                // Enable scan button
                runScanButton.disabled = false;
                
                // Show error message
                noResultsMessage.innerHTML = `
                    <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
                    <h5>Scan Failed</h5>
                    <p class="text-muted">An error occurred during the security scan.</p>
                `;
                noResultsMessage.style.display = 'block';
            });
        });
        
        function populateVulnerabilityTable(report) {
            const tableBody = document.getElementById('vulnerabilityTableBody');
            tableBody.innerHTML = '';
            
            // Flatten vulnerabilities by file
            const vulnerabilities = [];
            for (const file in report.details) {
                report.details[file].forEach(vuln => {
                    vulnerabilities.push({
                        file: file,
                        line: vuln.line,
                        type: vuln.type,
                        severity: vuln.severity,
                        description: vuln.description,
                        code: vuln.code
                    });
                });
            }
            
            // Sort by severity (critical first)
            const severityOrder = { 'critical': 0, 'high': 1, 'medium': 2, 'low': 3 };
            vulnerabilities.sort((a, b) => severityOrder[a.severity] - severityOrder[b.severity]);
            
            // Add rows to table
            vulnerabilities.forEach(vuln => {
                const row = document.createElement('tr');
                
                // Set row color based on severity
                if (vuln.severity === 'critical') {
                    row.classList.add('table-danger');
                } else if (vuln.severity === 'high') {
                    row.classList.add('table-warning');
                } else if (vuln.severity === 'medium') {
                    row.classList.add('table-info');
                }
                
                row.innerHTML = `
                    <td>${vuln.file}</td>
                    <td>${vuln.line}</td>
                    <td>${formatVulnerabilityType(vuln.type)}</td>
                    <td><span class="badge bg-${getSeverityBadgeColor(vuln.severity)}">${vuln.severity}</span></td>
                    <td>${vuln.description}</td>
                    <td><code>${escapeHtml(vuln.code)}</code></td>
                `;
                
                tableBody.appendChild(row);
            });
        }
        
        function createVulnerabilityChart(report) {
            const ctx = document.getElementById('vulnerabilityChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (vulnerabilityChart) {
                vulnerabilityChart.destroy();
            }
            
            // Prepare data for chart
            const labels = Object.keys(report.vulnerabilities_by_type).map(type => formatVulnerabilityType(type));
            const data = Object.values(report.vulnerabilities_by_type);
            
            // Create chart
            vulnerabilityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Vulnerabilities by Type',
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        function formatVulnerabilityType(type) {
            // Convert snake_case to Title Case
            return type.split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }
        
        function getSeverityBadgeColor(severity) {
            switch (severity) {
                case 'critical': return 'danger';
                case 'high': return 'warning';
                case 'medium': return 'info';
                case 'low': return 'secondary';
                default: return 'primary';
            }
        }
        
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
</script>
@endsection