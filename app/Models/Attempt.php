<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'submitted_code',
        'test_results',
        'is_successful',
        'score',
        'execution_time',
        'memory_used',
        'error_message',
        'ai_feedback',
        'hints_used',
        'time_spent',
    ];

    protected $casts = [
        'test_results' => 'array',
        'ai_feedback' => 'array',
        'is_successful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function heatmapLines()
    {
        return $this->hasMany(HeatmapLine::class);
    }
}

// app/Models/HeatmapLine.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeatmapLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'attempt_id',
        'line_number',
        'status',
        'message',
        'frequency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }
}
