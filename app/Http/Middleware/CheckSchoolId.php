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
        header('Access-Control-Allow-Origin:  *');
        //header('Access-Control-Allow-Origin:  http://localhost:4200');
        header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin');
        header('Access-Control-Allow-Methods:  GET, POST, PUT');
        dd($request->header('school_id'));
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
