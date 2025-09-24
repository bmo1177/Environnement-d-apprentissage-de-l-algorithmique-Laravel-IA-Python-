<?php
namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Attempt;
use App\Models\LearnerProfile;
use App\Models\Competency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $profile = $user->learnerProfile ?? LearnerProfile::create(['user_id' => $user->id]);
        
        $recentAttempts = $user->attempts()->with('challenge')->latest()->take(5)->get();
        $competencies = Competency::withCount('challenges')->get();
        
        $stats = [
            'total_attempts' => $profile->total_attempts,
            'success_rate' => $profile->total_attempts > 0 
                ? round(($profile->successful_attempts / $profile->total_attempts) * 100, 2) 
                : 0,
            'current_streak' => $profile->streak_days,
            'overall_score' => round($profile->overall_performance, 2),
        ];
        
        return view('student.dashboard', compact('profile', 'recentAttempts', 'competencies', 'stats'));
    }

    public function challenges()
    {
        $challenges = Challenge::with('competency')
            ->where('is_active', true)
            ->paginate(12);
            
        return view('student.challenges', compact('challenges'));
    }

    public function showChallenge(Challenge $challenge)
    {
        $user = auth()->user();
        $attempts = $user->attempts()
            ->where('challenge_id', $challenge->id)
            ->latest()
            ->take(5)
            ->get();
            
        return view('student.challenge', compact('challenge', 'attempts'));
    }

    public function submitSolution(Request $request, Challenge $challenge)
    {
        $request->validate([
            'code' => 'required|string',
            'time_spent' => 'integer|min:0',
        ]);

        $user = auth()->user();
        
        // Check attempt limit
        $attemptCount = $user->attempts()->where('challenge_id', $challenge->id)->count();
        if ($attemptCount >= $challenge->max_attempts) {
            return back()->with('error', 'Maximum attempts reached for this challenge.');
        }

        // Call Python evaluation service
        $response = Http::timeout(30)->post('http://python_service:8000/evaluate', [
            'code' => $request->code,
            'test_cases' => $challenge->test_cases,
            'language' => 'python',
        ]);

        $result = $response->json();
        
        // Create attempt record
        $attempt = Attempt::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge->id,
            'submitted_code' => $request->code,
            'test_results' => $result['test_results'] ?? [],
            'is_successful' => $result['success'] ?? false,
            'score' => $result['score'] ?? 0,
            'execution_time' => $result['execution_time'] ?? null,
            'memory_used' => $result['memory_used'] ?? null,
            'error_message' => $result['error'] ?? null,
            'time_spent' => $request->time_spent ?? 0,
        ]);

        // Update learner profile
        $this->updateLearnerProfile($user, $attempt, $challenge);

        // Generate AI feedback
        if ($attempt->is_successful || $attemptCount > 2) {
            $this->generateAiFeedback($attempt);
        }

        return redirect()->route('student.challenge', $challenge)
            ->with('success', $attempt->is_successful ? 'Solution accepted!' : 'Solution failed. Check the feedback.');
    }

    private function updateLearnerProfile($user, $attempt, $challenge)
    {
        $profile = $user->learnerProfile;
        $profile->total_attempts++;
        
        if ($attempt->is_successful) {
            $profile->successful_attempts++;
        }
        
        // Update competency scores
        $competencyScores = $profile->competency_scores ?? [];
        $competencyId = $challenge->competency_id;
        
        if (!isset($competencyScores[$competencyId])) {
            $competencyScores[$competencyId] = 0;
        }
        
        if ($attempt->is_successful) {
            $competencyScores[$competencyId] = min(100, $competencyScores[$competencyId] + 10);
        }
        
        $profile->competency_scores = $competencyScores;
        $profile->updatePerformanceMetrics();
        $profile->save();

        // Call Python profile update service
        Http::post('http://python_service:8000/update_profile', [
            'user_id' => $user->id,
            'attempt_data' => $attempt->toArray(),
            'challenge_data' => $challenge->toArray(),
        ]);
    }

    private function generateAiFeedback($attempt)
    {
        $response = Http::post('http://python_service:8000/recommend', [
            'attempt_id' => $attempt->id,
            'code' => $attempt->submitted_code,
            'test_results' => $attempt->test_results,
            'error_message' => $attempt->error_message,
        ]);

        if ($response->successful()) {
            $attempt->ai_feedback = $response->json();
            $attempt->save();
        }
    }

    public function profile()
    {
        $user = auth()->user();
        $profile = $user->learnerProfile;
        
        $competencyData = [];
        if ($profile && $profile->competency_scores) {
            $competencies = Competency::whereIn('id', array_keys($profile->competency_scores))->get();
            foreach ($competencies as $competency) {
                $competencyData[] = [
                    'name' => $competency->name,
                    'score' => $profile->competency_scores[$competency->id] ?? 0,
                    'domain' => $competency->domain,
                ];
            }
        }
        
        return view('student.profile', compact('profile', 'competencyData'));
    }
}