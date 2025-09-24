<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class EnhancedCsrfProtection extends BaseVerifier
{
    /**
     * Handle an incoming request with enhanced CSRF protection
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        // Skip CSRF verification for excluded URIs
        if ($this->isReading($request) || $this->inExceptArray($request)) {
            return $next($request);
        }

        // Add additional security headers
        $response = $next($request);
        
        // Add security headers to prevent clickjacking, XSS, etc.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Only set Content-Security-Policy on non-API routes
        if (!$request->is('api/*')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;"
            );
        }

        try {
            // Verify CSRF token
            if (!$this->tokensMatch($request)) {
                // Log potential CSRF attack
                Log::warning('CSRF token mismatch', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'user_id' => $request->user() ? $request->user()->id : 'guest'
                ]);
                
                throw new TokenMismatchException('CSRF token mismatch');
            }
        } catch (TokenMismatchException $e) {
            // If this is an AJAX request, return JSON error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'CSRF token mismatch. Please refresh the page and try again.'
                ], 419);
            }
            
            // Otherwise, redirect back with error message
            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Your session has expired. Please try again.');
        }

        return $response;
    }
}