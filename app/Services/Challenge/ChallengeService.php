<?php

namespace App\Services\Challenge;

use App\Models\Challenge;
use App\Models\Attempt;
use App\Models\User;
use App\Models\LearnerProfile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service class for Challenge-related business logic
 */
class ChallengeService
{
    /**
     * Get challenges for a specific student with attempt information
     *
     * @param User $user The student user
     * @return array Challenges with attempt information
     */
    public function getChallengesForStudent(User $user)
    {
        $challenges = Challenge::with(['competency', 'attempts' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->where('is_active', true)->get();

        return $challenges->map(function ($challenge) use ($user) {
            $attempts = $challenge->attempts->where('user_id', $user->id);
            $bestScore = $attempts->max('score') ?? 0;
            $attemptsCount = $attempts->count();
            $attemptsLeft = $challenge->max_attempts - $attemptsCount;
            $attemptsLeft = max(0, $attemptsLeft);
            
            return [
                'challenge' => $challenge,
                'best_score' => $bestScore,
                'attempts_count' => $attemptsCount,
                'attempts_left' => $attemptsLeft,
                'completed' => $bestScore >= $challenge->points,
            ];
        })->toArray();
    }

    /**
     * Submit a solution for evaluation
     *
     * @param User $user The student user
     * @param Challenge $challenge The challenge being attempted
     * @param string $solution The submitted solution code
     * @return array Result of the submission
     * @throws Exception If evaluation fails or attempt limit reached
     */
    public function submitSolution(User $user, Challenge $challenge, string $solution)
    {
        // Check if user has attempts left
        $attemptsCount = Attempt::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->count();
            
        if ($attemptsCount >= $challenge->max_attempts) {
            throw new Exception('Maximum attempts reached for this challenge');
        }
        
        // Call evaluation service
        try {
            $response = Http::timeout(30)->post(env('PYTHON_SERVICE_URL') . '/evaluate', [
                'code' => $solution,
                'test_cases' => json_decode($challenge->test_cases),
                'challenge_id' => $challenge->id
            ]);
            
            if (!$response->successful()) {
                Log::error('Evaluation service error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new Exception('Error evaluating solution: ' . $response->body());
            }
            
            $result = $response->json();
            
            // Create attempt record
            $attempt = Attempt::create([
                'user_id' => $user->id,
                'challenge_id' => $challenge->id,
                'code' => $solution,
                'result' => json_encode($result),
                'score' => $result['score'] ?? 0,
                'feedback' => $result['feedback'] ?? '',
                'execution_time' => $result['execution_time'] ?? 0,
                'passed' => $result['passed'] ?? false
            ]);
            
            // Update learner profile
            $this->updateLearnerProfile($user, $challenge, $result);
            
            // Generate AI feedback
            $attempt->ai_feedback = $this->generateAIFeedback($solution, $result);
            $attempt->save();
            
            return [
                'success' => true,
                'attempt' => $attempt,
                'result' => $result
            ];
        } catch (Exception $e) {
            Log::error('Solution submission error', [
                'user_id' => $user->id,
                'challenge_id' => $challenge->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Update the learner profile based on challenge results
     *
     * @param User $user The student user
     * @param Challenge $challenge The challenge attempted
     * @param array $result The evaluation result
     * @return void
     */
    protected function updateLearnerProfile(User $user, Challenge $challenge, array $result)
    {
        $profile = LearnerProfile::firstOrCreate(['user_id' => $user->id]);
        
        // Call profile update service
        try {
            Http::post(env('PYTHON_SERVICE_URL') . '/profile/update', [
                'user_id' => $user->id,
                'challenge_id' => $challenge->id,
                'competency_id' => $challenge->competency_id,
                'result' => $result,
                'current_profile' => $profile->profile_data ? json_decode($profile->profile_data, true) : []
            ]);
        } catch (Exception $e) {
            Log::error('Profile update error', [
                'user_id' => $user->id,
                'challenge_id' => $challenge->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate AI feedback for the submitted solution
     *
     * @param string $solution The submitted solution
     * @param array $result The evaluation result
     * @return string The AI feedback
     */
    protected function generateAIFeedback(string $solution, array $result)
    {
        try {
            $response = Http::post(env('PYTHON_SERVICE_URL') . '/feedback', [
                'code' => $solution,
                'result' => $result
            ]);
            
            if ($response->successful()) {
                return $response->json('feedback') ?? 'No feedback available';
            }
        } catch (Exception $e) {
            Log::error('AI feedback generation error', ['error' => $e->getMessage()]);
        }
        
        return 'Feedback generation failed. Please review your code manually.';
    }
}