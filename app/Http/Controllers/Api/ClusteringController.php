<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClusteringController extends Controller
{
    /**
     * Trigger the clustering analysis process
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerClustering(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'min_clusters' => 'sometimes|integer|min:2|max:10',
                'max_clusters' => 'sometimes|integer|min:2|max:10',
            ]);
            
            // Set default values if not provided
            $minClusters = $validated['min_clusters'] ?? 3;
            $maxClusters = $validated['max_clusters'] ?? 6;
            
            // Call Python service for clustering
            $response = Http::timeout(30)->post('http://localhost:8000/cluster', [
                'min_clusters' => $minClusters,
                'max_clusters' => $maxClusters,
            ]);
            
            if (!$response->successful()) {
                Log::error('Clustering service error', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error connecting to clustering service',
                ], 500);
            }
            
            $data = $response->json();
            
            return response()->json([
                'success' => true,
                'clusters' => $data['clusters'],
                'optimal_k' => $data['optimal_k'] ?? null,
                'silhouette_score' => $data['silhouette_score'] ?? null,
                'message' => 'Clustering completed successfully',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Clustering controller error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during clustering: ' . $e->getMessage(),
            ], 500);
        }
    }
}