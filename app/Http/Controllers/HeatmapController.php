<?php 
namespace App\Http\Controllers;

use App\Models\HeatmapLine;
use App\Models\Attempt;
use App\Models\Challenge;
use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    public function generateHeatmap(Attempt $attempt)
    {
        $lines = explode("\n", $attempt->submitted_code);
        $heatmapData = [];
        
        foreach ($attempt->test_results as $testResult) {
            if (isset($testResult['error_line'])) {
                $lineNum = $testResult['error_line'];
                $status = $testResult['passed'] ? 'correct' : 'error';
                
                HeatmapLine::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'line_number' => $lineNum,
                    ],
                    [
                        'user_id' => $attempt->user_id,
                        'challenge_id' => $attempt->challenge_id,
                        'status' => $status,
                        'message' => $testResult['message'] ?? null,
                        'frequency' => \DB::raw('frequency + 1'),
                    ]
                );
            }
        }
        
        return response()->json(['success' => true]);
    }

    public function getHeatmapData(Request $request)
    {
        $query = HeatmapLine::query();
        
        if ($request->has('challenge_id')) {
            $query->where('challenge_id', $request->challenge_id);
        }
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $heatmapLines = $query->get()->groupBy('line_number');
        
        return response()->json($heatmapLines);
    }
}