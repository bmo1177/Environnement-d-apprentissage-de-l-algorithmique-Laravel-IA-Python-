<?php
namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Competency;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = Challenge::with('competency')
            ->where('is_active', true)
            ->paginate(12);
            
        return view('challenges.index', compact('challenges'));
    }

    public function show(Challenge $challenge)
    {
        return view('challenges.show', compact('challenge'));
    }
}