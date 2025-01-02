<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSchoolId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Set CORS headers for testing (this will apply to all incoming requests)
    header('Access-Control-Allow-Origin: *');  // Allow all origins (for testing only)
    header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Authorization, school_id');  // Allow necessary headers including 'school_id'
    header('Access-Control-Allow-Methods: GET, POST, PUT');  // Allow certain HTTP methods

    // Check for 'school_id' header
    dd(dd($request->headers->all()));  // For debugging purposes
        if (!$request->hasHeader('school_id') || !$request->header('school_id')) {
            return response()->json([
                'status' => false,
                'error' => [
                    'message' => 'School id is required in header',
                    'status_code' => 400
                ]
            ], 400);
        }
    
        return $next($request);
    }
}
