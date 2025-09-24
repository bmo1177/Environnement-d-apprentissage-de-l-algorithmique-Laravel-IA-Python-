<?php

namespace App\Http\Controllers;

use App\Services\Security\SecurityScanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    protected $securityScanner;

    /**
     * Create a new controller instance.
     *
     * @param SecurityScanner $securityScanner
     */
    public function __construct(SecurityScanner $securityScanner)
    {
        $this->securityScanner = $securityScanner;
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the security dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('admin.security.dashboard');
    }

    /**
     * Run a security scan on the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function runScan(Request $request)
    {
        try {
            // Scan the app directory
            $appPath = base_path('app');
            $scanResults = $this->securityScanner->scanDirectory($appPath);
            
            // Generate a report
            $report = $this->securityScanner->generateReport($scanResults);
            
            // Log the results
            $this->securityScanner->logScanResults($report);
            
            return response()->json([
                'success' => true,
                'report' => $report
            ]);
        } catch (\Exception $e) {
            Log::error('Security scan failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Security scan failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the scan results.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function scanResults(Request $request)
    {
        // This would typically retrieve saved scan results from a database
        // For now, we'll just return the view
        return view('admin.security.scan_results');
    }
}