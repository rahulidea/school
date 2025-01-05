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

        // Set CORS headers to allow all origins, methods, and headers
    $response = $next($request);
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization, Origin');

    // Handle OPTIONS request for pre-flight requests
    if ($request->getMethod() == 'OPTIONS') {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization, Origin');
    }

    
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
