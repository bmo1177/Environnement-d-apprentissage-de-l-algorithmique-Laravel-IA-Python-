<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Challenge;
use App\Models\Attempt;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $studentCount = User::where('role', 'student')->count();
        $challengeCount = Challenge::count();
        $totalAttempts = Attempt::count();
        $successRate = Attempt::where('is_successful', true)->count() / max($totalAttempts, 1) * 100;
        
        $recentAttempts = Attempt::with(['user', 'challenge'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('teacher.dashboard', compact(
            'studentCount', 
            'challengeCount', 
            'totalAttempts', 
            'successRate',
            'recentAttempts'
        ));
    }

    public function students()
    {
        $students = User::where('role', 'student')
            ->with('learnerProfile')
            ->paginate(20);
            
        return view('teacher.students', compact('students'));
    }

    public function studentDetail($id)
    {
        $student = User::with(['learnerProfile', 'attempts.challenge'])->findOrFail($id);
        
        $stats = [
            'total_attempts' => $student->attempts->count(),
            'successful_attempts' => $student->attempts->where('is_successful', true)->count(),
            'challenges_attempted' => $student->attempts->unique('challenge_id')->count(),
            'average_score' => $student->attempts->avg('score'),
        ];
        
        $recentAttempts = $student->attempts()
            ->with('challenge')
            ->latest()
            ->take(10)
            ->get();
        
        return view('teacher.student-detail', compact('student', 'stats', 'recentAttempts'));
    }

    public function challenges()
    {
        $challenges = Challenge::with(['competency', 'attempts'])
            ->withCount('attempts')
            ->paginate(20);
            
        return view('teacher.challenges', compact('challenges'));
    }

    public function createChallenge()
    {
        $competencies = Competency::all();
        return view('teacher.challenge-create', compact('competencies'));
    }

    public function storeChallenge(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'competency_id' => 'required|exists:competencies,id',
            'difficulty' => 'required|in:easy,medium,hard',
            'starter_code' => 'nullable|string',
            'test_cases' => 'required|json',
            'hints' => 'nullable|json',
            'max_attempts' => 'integer|min:1|max:50',
            'time_limit' => 'integer|min:5|max:180',
            'points' => 'integer|min:10|max:1000',
        ]);

        Challenge::create($validated);
        
        return redirect()->route('teacher.challenges')
            ->with('success', 'Challenge created successfully!');
    }

    public function editChallenge(Challenge $challenge)
    {
        $competencies = Competency::all();
        return view('teacher.challenge-edit', compact('challenge', 'competencies'));
    }

    public function updateChallenge(Request $request, Challenge $challenge)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'problem_statement' => 'required|string',
            'competency_id' => 'required|exists:competencies,id',
            'difficulty' => 'required|in:easy,medium,hard',
            'starter_code' => 'nullable|string',
            'test_cases' => 'required|json',
            'hints' => 'nullable|json',
            'max_attempts' => 'integer|min:1|max:50',
            'time_limit' => 'integer|min:5|max:180',
            'points' => 'integer|min:10|max:1000',
            'is_active' => 'boolean',
        ]);

        $challenge->update($validated);
        
        return redirect()->route('teacher.challenges')
            ->with('success', 'Challenge updated successfully!');
    }

    public function clusters()
    {
        try {
            // Get clustering from Python service
            $response = Http::timeout(10)->post('http://localhost:8000/cluster', [
                'min_clusters' => 3,
                'max_clusters' => 6,
            ]);   
            
            if ($response->successful()) {
                $data = $response->json();
                $clusters = $data['clusters'] ?? [];
                $error = null;
            } else {
                $clusters = [];
                $error = 'Clustering service returned an error: ' . ($response->json()['detail'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            $clusters = [];
            $error = 'Could not connect to clustering service: ' . $e->getMessage();
        }
        
        return view('teacher.clusters', compact('clusters', 'error'));
    }

    public function analytics()
    {
        $competencyPerformance = Competency::with(['challenges.attempts'])->get()->map(function ($competency) {
            $attempts = $competency->challenges->flatMap->attempts;
            return [
                'name' => $competency->name,
                'total_attempts' => $attempts->count(),
                'success_rate' => $attempts->count() > 0 
                    ? $attempts->where('is_successful', true)->count() / $attempts->count() * 100 
                    : 0,
                'average_score' => $attempts->avg('score') ?? 0,
            ];
        });
        
        return view('teacher.analytics', compact('competencyPerformance'));
    }
}