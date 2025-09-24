{{-- resources/views/teacher/challenge-create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('teacher.challenges') }}">Challenges</a></li>
                <li class="breadcrumb-item active">Create New Challenge</li>
            </ol>
        </nav>
        <h1 class="h2 mb-0">Create New Challenge</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('teacher.challenge.store') }}" method="POST" id="challengeForm">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Challenge Title *</label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           required
                                           placeholder="e.g., Two Sum Algorithm">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="difficulty" class="form-label">Difficulty *</label>
                                    <select class="form-select @error('difficulty') is-invalid @enderror" 
                                            id="difficulty" 
                                            name="difficulty" 
                                            required>
                                        <option value="">Select difficulty</option>
                                        <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                                        <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                                    </select>
                                    @error('difficulty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="competency_id" class="form-label">Competency *</label>
                                    <select class="form-select @error('competency_id') is-invalid @enderror" 
                                            id="competency_id" 
                                            name="competency_id" 
                                            required>
                                        <option value="">Select competency</option>
                                        @foreach($competencies as $competency)
                                            <option value="{{ $competency->id }}" 
                                                    {{ old('competency_id') == $competency->id ? 'selected' : '' }}>
                                                {{ $competency->name }} ({{ $competency->domain }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('competency_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="points" class="form-label">Points</label>
                                    <input type="number" 
                                           class="form-control @error('points') is-invalid @enderror" 
                                           id="points" 
                                           name="points" 
                                           value="{{ old('points', 100) }}" 
                                           min="10" 
                                           max="1000">
                                    @error('points')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="time_limit" class="form-label">Time Limit (min)</label>
                                    <input type="number" 
                                           class="form-control @error('time_limit') is-invalid @enderror" 
                                           id="time_limit" 
                                           name="time_limit" 
                                           value="{{ old('time_limit', 30) }}" 
                                           min="5" 
                                           max="180">
                                    @error('time_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Short Description *</label>
                            <input type="text" 
                                   class="form-control @error('description') is-invalid @enderror" 
                                   id="description" 
                                   name="description" 
                                   value="{{ old('description') }}" 
                                   required
                                   placeholder="Brief description of what the challenge teaches"
                                   maxlength="255">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Problem Statement -->
                    <div class="mb-4">
                        <h5 class="card-title">
                            <i class="bi bi-file-text me-2"></i>Problem Statement
                        </h5>
                        
                        <div class="mb-3">
                            <label for="problem_statement" class="form-label">Detailed Problem Description *</label>
                            <textarea class="form-control @error('problem_statement') is-invalid @enderror" 
                                      id="problem_statement" 
                                      name="problem_statement" 
                                      rows="8" 
                                      required
                                      placeholder="Provide a detailed description of the problem students need to solve...">{{ old('problem_statement') }}</textarea>
                            @error('problem_statement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Use clear, precise language. Include examples and constraints.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="starter_code" class="form-label">Starter Code (Optional)</label>
                            <textarea class="form-control @error('starter_code') is-invalid @enderror" 
                                      id="starter_code" 
                                      name="starter_code" 
                                      rows="6"
                                      placeholder="def solve_problem(input_data):&#10;    # Your implementation here&#10;    return result">{{ old('starter_code') }}</textarea>
                            @error('starter_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Provide a function template to help students get started.
                            </div>
                        </div>
                    </div>

                    <!-- Test Cases -->
                    <div class="mb-4">
                        <h5 class="card-title">
                            <i class="bi bi-check-square me-2"></i>Test Cases
                        </h5>
                        
                        <div class="mb-3">
                            <label for="test_cases" class="form-label">Test Cases (JSON Format) *</label>
                            <textarea class="form-control @error('test_cases') is-invalid @enderror" 
                                      id="test_cases" 
                                      name="test_cases" 
                                      rows="8" 
                                      required
                                      placeholder='[
    {"input": {"nums": [2, 7, 11, 15], "target": 9}, "output": [0, 1]},
    {"input": {"nums": [3, 2, 4], "target": 6}, "output": [1, 2]}
]'>{{ old('test_cases') }}</textarea>
                            @error('test_cases')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Define test cases in JSON format. Include at least 3 test cases for thorough validation.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-secondary" onclick="validateTestCases()">
                                    <i class="bi bi-check-circle me-1"></i>Validate JSON
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-info" onclick="generateSampleTestCases()">
                                    <i class="bi bi-magic me-1"></i>Generate Sample
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hints -->
                    <div class="mb-4">
                        <h5 class="card-title">
                            <i class="bi bi-lightbulb me-2"></i>Hints (Optional)
                        </h5>
                        
                        <div class="mb-3">
                            <label for="hints" class="form-label">Hints for Students</label>
                            <textarea class="form-control @error('hints') is-invalid @enderror" 
                                      id="hints" 
                                      name="hints" 
                                      rows="4"
                                      placeholder='[
    "Consider using a hash map to store seen values",
    "Think about the time complexity of your solution"
]'>{{ old('hints') }}</textarea>
                            @error('hints')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Provide helpful hints in JSON array format. These will be shown after failed attempts.
                            </div>
                        </div>
                    </div>

                    <!-- Challenge Settings -->
                    <div class="mb-4">
                        <h5 class="card-title">
                            <i class="bi bi-gear me-2"></i>Challenge Settings
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_attempts" class="form-label">Maximum Attempts</label>
                                    <input type="number" 
                                           class="form-control @error('max_attempts') is-invalid @enderror" 
                                           id="max_attempts" 
                                           name="max_attempts" 
                                           value="{{ old('max_attempts', 5) }}" 
                                           min="1" 
                                           max="50">
                                    @error('max_attempts')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active (visible to students)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('teacher.challenges') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancel
                            </a>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="previewChallenge()">
                                <i class="bi bi-eye me-1"></i>Preview
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Create Challenge
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Help Sidebar -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-question-circle me-2"></i>Challenge Creation Guide
                </h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="helpAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                Writing Good Problem Statements
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    <li>• Be clear and specific about requirements</li>
                                    <li>• Include input/output examples</li>
                                    <li>• Specify constraints and edge cases</li>
                                    <li>• Use simple, understandable language</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Test Case Best Practices
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    <li>• Include at least 3-5 test cases</li>
                                    <li>• Cover normal cases and edge cases</li>
                                    <li>• Test boundary conditions</li>
                                    <li>• Ensure expected outputs are correct</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                JSON Format Examples
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                <strong>Test Cases:</strong>
                                <pre class="small bg-light p-2 mt-1">[
  {
    "input": {"n": 5},
    "output": 120
  }
]</pre>
                                <strong>Hints:</strong>
                                <pre class="small bg-light p-2 mt-1">[
  "Use recursion",
  "Consider base cases"
]</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Templates -->
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightning me-2"></i>Quick Templates
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="loadTemplate('array')">
                        Array Problem Template
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="loadTemplate('recursion')">
                        Recursion Template
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="loadTemplate('sorting')">
                        Sorting Algorithm Template
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="loadTemplate('graph')">
                        Graph Problem Template
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Challenge Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close Preview</button>
                <button type="button" class="btn btn-primary" onclick="submitFromPreview()">Create Challenge</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function validateTestCases() {
    const testCasesInput = document.getElementById('test_cases');
    const testCases = testCasesInput.value.trim();
    
    if (!testCases) {
        alert('Please enter test cases first.');
        return;
    }
    
    try {
        const parsed = JSON.parse(testCases);
        if (!Array.isArray(parsed)) {
            throw new Error('Test cases must be an array');
        }
        
        // Validate structure
        parsed.forEach((testCase, index) => {
            if (!testCase.hasOwnProperty('input') || !testCase.hasOwnProperty('output')) {
                throw new Error(`Test case ${index + 1} must have 'input' and 'output' properties`);
            }
        });
        
        alert(`✓ Valid JSON format with ${parsed.length} test case(s)`);
        testCasesInput.classList.remove('is-invalid');
        testCasesInput.classList.add('is-valid');
    } catch (error) {
        alert(`❌ Invalid JSON format: ${error.message}`);
        testCasesInput.classList.remove('is-valid');
        testCasesInput.classList.add('is-invalid');
    }
}

function generateSampleTestCases() {
    const sampleTestCases = [
        {
            "input": {"nums": [2, 7, 11, 15], "target": 9},
            "output": [0, 1]
        },
        {
            "input": {"nums": [3, 2, 4], "target": 6},
            "output": [1, 2]
        },
        {
            "input": {"nums": [3, 3], "target": 6},
            "output": [0, 1]
        }
    ];
    
    document.getElementById('test_cases').value = JSON.stringify(sampleTestCases, null, 2);
}

function loadTemplate(type) {
    const templates = {
        array: {
            title: "Two Sum Problem",
            description: "Find two numbers in an array that add up to a target sum",
            problem_statement: "Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target.\n\nYou may assume that each input would have exactly one solution, and you may not use the same element twice.\n\nExample:\nInput: nums = [2,7,11,15], target = 9\nOutput: [0,1]\nExplanation: Because nums[0] + nums[1] == 9, we return [0, 1].",
            starter_code: "def two_sum(nums, target):\n    # Your implementation here\n    return []",
            test_cases: JSON.stringify([
                {"input": {"nums": [2, 7, 11, 15], "target": 9}, "output": [0, 1]},
                {"input": {"nums": [3, 2, 4], "target": 6}, "output": [1, 2]},
                {"input": {"nums": [3, 3], "target": 6}, "output": [0, 1]}
            ], null, 2),
            hints: JSON.stringify([
                "Consider using a hash map to store previously seen values",
                "For each number, check if target - number exists in the hash map"
            ], null, 2)
        },
        recursion: {
            title: "Factorial Calculator",
            description: "Calculate the factorial of a given number using recursion",
            problem_statement: "Write a recursive function to calculate the factorial of a non-negative integer n.\n\nFactorial of n (n!) is the product of all positive integers less than or equal to n.\nFor example: 5! = 5 × 4 × 3 × 2 × 1 = 120\n\nSpecial case: 0! = 1",
            starter_code: "def factorial(n):\n    # Your recursive implementation here\n    pass",
            test_cases: JSON.stringify([
                {"input": {"n": 5}, "output": 120},
                {"input": {"n": 0}, "output": 1},
                {"input": {"n": 1}, "output": 1},
                {"input": {"n": 7}, "output": 5040}
            ], null, 2),
            hints: JSON.stringify([
                "Base case: factorial of 0 or 1 is 1",
                "Recursive case: n! = n × (n-1)!"
            ], null, 2)
        }
        // Add more templates as needed
    };
    
    const template = templates[type];
    if (template) {
        Object.keys(template).forEach(key => {
            const element = document.getElementById(key);
            if (element) {
                element.value = template[key];
            }
        });
    }
}

function previewChallenge() {
    const formData = new FormData(document.getElementById('challengeForm'));
    const previewContent = document.getElementById('previewContent');
    
    // Generate preview HTML
    const title = formData.get('title') || 'Untitled Challenge';
    const difficulty = formData.get('difficulty') || 'medium';
    const description = formData.get('description') || '';
    const problemStatement = formData.get('problem_statement') || '';
    
    let testCases = [];
    try {
        testCases = JSON.parse(formData.get('test_cases') || '[]');
    } catch (e) {
        testCases = [];
    }
    
    const previewHtml = `
        <div class="challenge-preview">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3>${title}</h3>
                    <span class="badge bg-${difficulty === 'easy' ? 'success' : difficulty === 'medium' ? 'warning' : 'danger'}">
                        ${difficulty.charAt(0).toUpperCase() + difficulty.slice(1)}
                    </span>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary">${formData.get('points') || 100} points</div>
                </div>
            </div>
            
            <div class="mb-3">
                <h5>Description</h5>
                <p>${description}</p>
            </div>
            
            <div class="mb-3">
                <h5>Problem Statement</h5>
                <div class="bg-light p-3 rounded">
                    <pre>${problemStatement}</pre>
                </div>
            </div>
            
            ${testCases.length > 0 ? `
                <div class="mb-3">
                    <h5>Example Test Cases</h5>
                    ${testCases.slice(0, 2).map((testCase, index) => `
                        <div class="card mb-2">
                            <div class="card-body">
                                <h6>Example ${index + 1}</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Input:</strong>
                                        <pre class="small">${JSON.stringify(testCase.input, null, 2)}</pre>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Expected Output:</strong>
                                        <pre class="small">${JSON.stringify(testCase.output, null, 2)}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            ` : ''}
            
            <div class="row">
                <div class="col-md-4">
                    <strong>Time Limit:</strong> ${formData.get('time_limit') || 30} minutes
                </div>
                <div class="col-md-4">
                    <strong>Max Attempts:</strong> ${formData.get('max_attempts') || 5}
                </div>
                <div class="col-md-4">
                    <strong>Status:</strong> ${formData.get('is_active') ? 'Active' : 'Inactive'}
                </div>
            </div>
        </div>
    `;
    
    previewContent.innerHTML = previewHtml;
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

function submitFromPreview() {
    // Close modal and submit form
    bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
    document.getElementById('challengeForm').submit();
}

// Form validation before submit
document.getElementById('challengeForm').addEventListener('submit', function(e) {
    // Validate test cases JSON
    const testCasesInput = document.getElementById('test_cases');
    if (testCasesInput.value.trim()) {
        try {
            JSON.parse(testCasesInput.value);
        } catch (error) {
            e.preventDefault();
            alert('Please fix the test cases JSON format before submitting.');
            testCasesInput.focus();
            return;
        }
    }
    
    // Validate hints JSON if provided
    const hintsInput = document.getElementById('hints');
    if (hintsInput.value.trim()) {
        try {
            JSON.parse(hintsInput.value);
        } catch (error) {
            e.preventDefault();
            alert('Please fix the hints JSON format before submitting.');
            hintsInput.focus();
            return;
        }
    }
});
</script>
@endpush