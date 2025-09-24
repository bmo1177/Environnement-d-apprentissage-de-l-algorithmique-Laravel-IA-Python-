{{-- resources/views/student/challenge.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.challenges') }}">Challenges</a></li>
                <li class="breadcrumb-item active">{{ $challenge->title }}</li>
            </ol>
        </nav>
        <h1 class="h2 mb-0">
            <span class="badge bg-{{ $challenge->difficulty === 'easy' ? 'success' : ($challenge->difficulty === 'medium' ? 'warning' : 'danger') }} me-2">
                {{ ucfirst($challenge->difficulty) }}
            </span>
            {{ $challenge->title }}
        </h1>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleInstructions">
                <i class="bi bi-eye me-1"></i>Toggle Instructions
            </button>
            @if($attempts->count() > 0)
                <button type="button" class="btn btn-sm btn-outline-info" id="showHints">
                    <i class="bi bi-lightbulb me-1"></i>Hints
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- Challenge Information Panel -->
    <div class="col-lg-5" id="instructionsPanel">
        <!-- Challenge Header Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="text-muted small">Points</div>
                        <div class="fw-bold text-primary fs-4">{{ $challenge->points }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Time Limit</div>
                        <div class="fw-bold">{{ $challenge->time_limit }}min</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Attempts</div>
                        <div class="fw-bold">{{ $attempts->count() }}/{{ $challenge->max_attempts }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted small">Best Score</div>
                        <div class="fw-bold {{ $attempts->max('score') >= 70 ? 'text-success' : 'text-warning' }}">
                            {{ $attempts->max('score') ?? 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Challenge Description -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>Problem Description
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">{{ $challenge->description }}</p>
                
                <h6 class="mt-3">Problem Statement</h6>
                <div class="bg-light p-3 rounded">
                    <pre class="mb-0">{{ $challenge->problem_statement }}</pre>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-tag me-1"></i>Competency: {{ $challenge->competency->name }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Test Cases (Sample) -->
        @if($challenge->test_cases && count($challenge->test_cases) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-check-square me-2"></i>Example Test Cases
                    </h6>
                </div>
                <div class="card-body">
                    @php $sampleTests = array_slice($challenge->test_cases, 0, 2); @endphp
                    @foreach($sampleTests as $index => $testCase)
                        <div class="mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <strong>Example {{ $index + 1 }}:</strong>
                            <div class="mt-2">
                                <div class="d-flex">
                                    <div class="me-4">
                                        <small class="text-muted">Input:</small>
                                        <div class="bg-light p-2 rounded">
                                            <code>{{ json_encode($testCase['input'] ?? []) }}</code>
                                        </div>
                                    </div>
                                    <div>
                                        <small class="text-muted">Expected Output:</small>
                                        <div class="bg-light p-2 rounded">
                                            <code>{{ json_encode($testCase['output'] ?? []) }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if(count($challenge->test_cases) > 2)
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ count($challenge->test_cases) - 2 }} more test cases will be used for evaluation.
                        </small>
                    @endif
                </div>
            </div>
        @endif

        <!-- Hints (if available and attempts > 0) -->
        @if($challenge->hints && count($challenge->hints) > 0 && $attempts->count() > 0)
            <div class="card mb-4" id="hintsCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Hints
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($challenge->hints as $index => $hint)
                        <div class="alert alert-info py-2">
                            <strong>Hint {{ $index + 1 }}:</strong> {{ $hint }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Code Editor and Submission Panel -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-code-slash me-2"></i>Code Editor
                </h5>
                <div class="d-flex align-items-center">
                    <small class="text-muted me-3" id="timer">Time: 00:00</small>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="resetCode">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="formatCode">
                            <i class="bi bi-braces"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <form id="solutionForm" action="{{ route('student.submit', $challenge) }}" method="POST">
                    @csrf
                    
                    <!-- Code Input Area -->
                    {{-- TODO: Replace with CodeMirror or Monaco Editor --}}
                    <textarea 
                        name="code" 
                        id="code" 
                        class="form-control code-editor border-0" 
                        rows="20" 
                        placeholder="# Write your solution here...
# Example:
def solve_problem(input_data):
    # Your algorithm implementation
    return result"
                        style="resize: none; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace; font-size: 14px;">{{ $challenge->starter_code ?? '' }}</textarea>
                    
                    {{-- 
                    TODO: Integration with CodeMirror/Monaco:
                    
                    <div id="codeEditor" style="height: 400px;"></div>
                    <script>
                        // CodeMirror example:
                        var editor = CodeMirror(document.getElementById('codeEditor'), {
                            mode: 'python',
                            theme: 'monokai',
                            lineNumbers: true,
                            value: '{{ $challenge->starter_code ?? '' }}'
                        });
                        
                        // Monaco Editor example:
                        require.config({ paths: { vs: 'https://unpkg.com/monaco-editor@latest/min/vs' } });
                        require(['vs/editor/editor.main'], function () {
                            var editor = monaco.editor.create(document.getElementById('codeEditor'), {
                                value: '{{ $challenge->starter_code ?? '' }}',
                                language: 'python',
                                theme: 'vs-dark'
                            });
                        });
                    </script>
                    --}}
                    
                    <input type="hidden" name="time_spent" id="timeSpent" value="0">
                </form>
            </div>
            
            <!-- Action Buttons -->
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @php
                            $remainingAttempts = $challenge->max_attempts - $attempts->count();
                            $canSubmit = $remainingAttempts > 0;
                        @endphp
                        
                        @if(!$canSubmit)
                            <span class="text-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                No attempts remaining
                            </span>
                        @else
                            <span class="text-muted">
                                {{ $remainingAttempts }} attempt{{ $remainingAttempts != 1 ? 's' : '' }} remaining
                            </span>
                        @endif
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary" id="runCode" {{ !$canSubmit ? 'disabled' : '' }}>
                            <i class="bi bi-play-circle me-1"></i>Test Run
                        </button>
                        <button type="button" class="btn btn-primary" id="submitCode" {{ !$canSubmit ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle me-1"></i>Submit Solution
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="card mt-4" id="resultsPanel" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-terminal me-2"></i>Execution Results
                </h5>
            </div>
            <div class="card-body" id="resultsContent">
                <!-- Results will be populated here -->
            </div>
        </div>

        <!-- Heatmap Visualization Placeholder -->
        <div class="card mt-4" id="heatmapPanel" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-map me-2"></i>Code Analysis Heatmap
                </h5>
            </div>
            <div class="card-body">
                <div class="heatmap-placeholder p-4 text-center" style="height: 200px;">
                    <i class="bi bi-bar-chart fs-1 text-muted mb-2"></i>
                    <p class="text-muted mb-0">Line-by-line code analysis visualization</p>
                    <small class="text-muted">
                        {{-- TODO: Integrate heatmap from FastAPI /heatmap/{challengeId} endpoint --}}
                        This will show performance hotspots and error patterns in your code
                    </small>
                </div>
                <!-- Concrete heatmap area (will be populated by JS) -->
                <div id="heatmapLines" class="mt-3" style="display: none;">
                    <!-- JS will inject a small per-line summary here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Previous Attempts -->
@if($attempts->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>Previous Attempts
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Attempt</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attempts as $attempt)
                        <tr>
                            <td>#{{ $loop->iteration }}</td>
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
                                    <span class="badge bg-success">Passed</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>
                                @if($attempt->time_spent)
                                    {{ gmdate("H:i:s", $attempt->time_spent) }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $attempt->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="loadAttemptCode({{ $attempt->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($attempt->ai_feedback)
                                        <button class="btn btn-outline-info" onclick="showAttemptFeedback({{ $attempt->id }})">
                                            <i class="bi bi-robot"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let startTime = Date.now();
let timerInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize timer
    startTimer();
    
    // Toggle instructions panel
    document.getElementById('toggleInstructions').addEventListener('click', function() {
        const panel = document.getElementById('instructionsPanel');
        const isVisible = panel.style.display !== 'none';
        panel.style.display = isVisible ? 'none' : 'block';
        this.innerHTML = isVisible 
            ? '<i class="bi bi-eye me-1"></i>Show Instructions'
            : '<i class="bi bi-eye-slash me-1"></i>Hide Instructions';
    });
    
    // Show hints
    const hintsButton = document.getElementById('showHints');
    if (hintsButton) {
        hintsButton.addEventListener('click', function() {
            const hintsCard = document.getElementById('hintsCard');
            hintsCard.style.display = hintsCard.style.display === 'none' ? 'block' : 'none';
        });
    }
    
    // Reset code
    document.getElementById('resetCode').addEventListener('click', function() {
        if (confirm('Are you sure you want to reset your code? This will restore the starter code.')) {
            document.getElementById('code').value = `{{ addslashes($challenge->starter_code ?? '') }}`;
        }
    });
    
    // Format code (basic implementation)
    document.getElementById('formatCode').addEventListener('click', function() {
        const code = document.getElementById('code').value;
        // Basic Python formatting (this would be better with a proper formatter)
        const formatted = code.split('\n').map(line => line.trim()).join('\n');
        document.getElementById('code').value = formatted;
    });
    
    // Test run (dry run without submitting)
    document.getElementById('runCode').addEventListener('click', function() {
        const code = document.getElementById('code').value.trim();
        if (!code) {
            alert('Please write some code first!');
            return;
        }
        
        testCode(code, false);
    });
    
    // Submit solution
    document.getElementById('submitCode').addEventListener('click', function() {
        const code = document.getElementById('code').value.trim();
        if (!code) {
            alert('Please write some code first!');
            return;
        }
        
        if (confirm('Are you sure you want to submit this solution? This will count as one attempt.')) {
            submitSolution(code);
        }
    });
});

function startTimer() {
    timerInterval = setInterval(function() {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        
        document.getElementById('timer').textContent = 
            `Time: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        document.getElementById('timeSpent').value = elapsed;
    }, 1000);
}

function testCode(code, submit = false) {
    const resultsPanel = document.getElementById('resultsPanel');
    const resultsContent = document.getElementById('resultsContent');
    
    resultsPanel.style.display = 'block';
    resultsContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Running...</span>
            </div>
            <p class="mt-2 mb-0">Executing your code...</p>
        </div>
    `;
    
    // Call Python evaluation service
    fetch('/api/evaluate-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            code: code,
            challenge_id: {{ $challenge->id }},
            test_run: !submit,
            time_spent: document.getElementById('timeSpent').value
        })
    })
    .then(response => response.json())
    .then(data => {
        displayResults(data, submit);
        
        if (submit && data.success) {
            // Reload page to show updated attempts
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    })
    .catch(error => {
        resultsContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error occurred while executing code: ${error.message}
            </div>
        `;
    });
}

function submitSolution(code) {
    // Update form and submit
    document.getElementById('code').value = code;
    document.getElementById('solutionForm').submit();
}

function displayResults(data, isSubmission = false) {
    const resultsContent = document.getElementById('resultsContent');
    let html = '';
    
    if (isSubmission) {
        html += `
            <div class="alert alert-${data.success ? 'success' : 'warning'} mb-3">
                <i class="bi bi-${data.success ? 'check' : 'exclamation'}-circle me-2"></i>
                <strong>${data.success ? 'Submission Successful!' : 'Submission Completed'}</strong>
                Your score: ${data.score ?? 0}%
            </div>
        `;
    }
    
    if (data.test_results && data.test_results.length > 0) {
        html += `
            <h6><i class="bi bi-list-check me-2"></i>Test Results:</h6>
            <div class="row">
        `;
        
        data.test_results.forEach((result, index) => {
            const passed = result.passed || false;
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card ${passed ? 'border-success' : 'border-danger'}">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Test ${index + 1}</strong>
                                <span class="badge bg-${passed ? 'success' : 'danger'}">
                                    ${passed ? 'PASS' : 'FAIL'}
                                </span>
                            </div>
                            ${result.error ? `<small class="text-danger">${escapeHtml(result.error)}</small>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
    }
    
    if (data.error) {
        html += `
            <div class="alert alert-danger">
                <h6><i class="bi bi-bug me-2"></i>Execution Error:</h6>
                <pre class="mb-0">${escapeHtml(data.error)}</pre>
            </div>
        `;
    }
    
    if (data.ai_feedback) {
        html += `
            <div class="alert alert-info">
                <h6><i class="bi bi-robot me-2"></i>AI Feedback:</h6>
                <p class="mb-0">${escapeHtml(data.ai_feedback.message || 'Analysis complete.')}</p>
            </div>
        `;
    }
    
    resultsContent.innerHTML = html || `
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Code executed successfully with no specific feedback.
        </div>
    `;
    
    // Show heatmap if available in response, otherwise try to fetch it
    if (data.heatmap) {
        showHeatmap(data.heatmap);
    } else {
        // Attempt to fetch aggregated heatmap for this challenge (optional)
        fetchHeatmap({{ $challenge->id }});
    }
}

/**
 * Fetch heatmap data for a challenge (if not provided directly by evaluate response).
 * Expected format (example):
 * [{ line: 1, hits: 5 }, { line: 2, hits: 0 }, { line: 3, hits: 12 }, ...]
 */
function fetchHeatmap(challengeId) {
    // Endpoint example: /api/heatmap/{id}
    fetch(`/api/heatmap/${challengeId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(r => {
        if (!r.ok) throw new Error('No heatmap available');
        return r.json();
    })
    .then(data => {
        if (data && data.length) showHeatmap(data);
    })
    .catch(() => {
        // quietly ignore if heatmap is not available
    });
}

/**
 * Render a simple heatmap summary under the heatmap panel.
 * For a proper overlay, replace the <textarea> with CodeMirror/Monaco and use its line decorations API.
 */
function showHeatmap(heatmapData) {
    if (!Array.isArray(heatmapData) || heatmapData.length === 0) {
        return;
    }

    // Make sure heatmap panel is visible
    document.getElementById('heatmapPanel').style.display = 'block';
    const heatmapLines = document.getElementById('heatmapLines');
    heatmapLines.style.display = 'block';
    heatmapLines.innerHTML = '';

    // Prepare code lines to map indices (use the editor content lines count)
    const code = document.getElementById('code').value || '';
    const codeLines = code.split('\n');
    const maxHits = Math.max(...heatmapData.map(h => h.hits || 0), 1);

    // Build a compact visual: line number, small bar representing hits, and optional message
    heatmapData.forEach(h => {
        const lineIndex = h.line; // expected 1-based
        const hits = h.hits || 0;
        const severity = Math.round((hits / maxHits) * 100); // 0-100

        const lineText = codeLines[lineIndex - 1] || '';
        const shortText = lineText.length > 80 ? lineText.slice(0, 80) + '…' : lineText;

        const colorClass = hits === 0 ? 'bg-light' : (severity > 66 ? 'bg-danger' : (severity > 33 ? 'bg-warning' : 'bg-info'));

        const row = document.createElement('div');
        row.className = 'd-flex align-items-center mb-2';

        row.innerHTML = `
            <div style="width:55px" class="text-muted small">L ${lineIndex}</div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center">
                    <div class="progress w-100 me-2" style="height:10px;">
                        <div class="progress-bar ${colorClass}" role="progressbar" style="width: ${severity}%;" aria-valuenow="${severity}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted ms-2">${hits} hits</small>
                </div>
                <div class="small text-truncate mt-1" title="${escapeHtml(lineText)}">${escapeHtml(shortText)}</div>
            </div>
            <div class="ms-3">
                <button class="btn btn-sm btn-outline-secondary" onclick="jumpToLine(${lineIndex})">View</button>
            </div>
        `;
        heatmapLines.appendChild(row);
    });
}

/**
 * When clicking "View" on a heatmap line: scroll textarea to that line.
 * If using a code editor, replace behavior to use editor API to reveal line.
 */
function jumpToLine(n) {
    const textarea = document.getElementById('code');
    const lines = textarea.value.split('\n');
    let pos = 0;
    for (let i = 0; i < Math.min(n - 1, lines.length); i++) {
        pos += lines[i].length + 1; // +1 for newline
    }
    textarea.focus();
    textarea.setSelectionRange(pos, pos);
    // scroll so the selected line is visible
    const lineHeight = parseInt(window.getComputedStyle(textarea).lineHeight) || 18;
    textarea.scrollTop = Math.max(0, (n - 5) * lineHeight);
}

/**
 * Load previous attempt code into the editor (for viewing).
 * Endpoint example: GET /api/attempts/{id}
 * Adjust route as needed to match your backend.
 */
function loadAttemptCode(attemptId) {
    fetch(`/api/attempts/${attemptId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.code) {
            // if using CodeMirror/Monaco, set value on editor instead
            document.getElementById('code').value = data.code;
            // Show attempt results in the results panel
            if (data.test_results || data.ai_feedback) {
                displayResults(data, false);
            }
            // If the attempt has heatmap data, show it
            if (data.heatmap) showHeatmap(data.heatmap);
            window.scrollTo({ top: document.getElementById('resultsPanel').offsetTop - 20, behavior: 'smooth' });
        } else {
            alert('Attempt data not found. Make sure the API path is correct.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to load attempt. Check console for details.');
    });
}

/**
 * Show AI feedback for a previous attempt (modal or results panel).
 * Endpoint example: GET /api/attempts/{id}/feedback
 */
function showAttemptFeedback(attemptId) {
    fetch(`/api/attempts/${attemptId}/feedback`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.message) {
            // display in a modal-like alert (you can implement a bootstrap modal instead)
            const resultsContent = document.getElementById('resultsContent');
            document.getElementById('resultsPanel').style.display = 'block';
            resultsContent.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="bi bi-robot me-2"></i>AI Feedback (Attempt #${attemptId}):</h6>
                    <p class="mb-0">${escapeHtml(data.message)}</p>
                </div>
            `;
        } else {
            alert('No AI feedback available for this attempt.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed to load AI feedback.');
    });
}

/* Utility: escape HTML to avoid injection in innerHTML */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
}
</script>
@endpush


