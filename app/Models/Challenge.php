<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'problem_statement',
        'competency_id',
        'difficulty',
        'starter_code',
        'test_cases',
        'hints',
        'max_attempts',
        'time_limit',
        'points',
        'is_active',
    ];

    protected $casts = [
        'test_cases' => 'array',
        'hints' => 'array',
        'is_active' => 'boolean',
    ];

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function heatmapLines()
    {
        return $this->hasMany(HeatmapLine::class);
    }
}
