<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'problem_solving_score',
        'logical_reasoning_score',
        'pattern_recognition_score',
        'abstraction_score',
        'total_attempts',
        'successful_attempts',
        'average_time_per_challenge',
        'hints_used',
        'preferred_challenge_types',
        'engagement_level',
        'persistence_score',
        'streak_days',
        'last_active_date',
        'achievements',
        'learning_style',
        'pace',
        'competency_scores',
        'overall_performance',
    ];

    protected $casts = [
        'preferred_challenge_types' => 'array',
        'achievements' => 'array',
        'competency_scores' => 'array',
        'last_active_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatePerformanceMetrics()
    {
        $successRate = $this->total_attempts > 0 
            ? ($this->successful_attempts / $this->total_attempts) * 100 
            : 0;
        
        $this->overall_performance = $successRate;
        $this->save();
    }
}