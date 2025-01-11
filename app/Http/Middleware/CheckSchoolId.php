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
        if (!$request->filled('school_id')) {
            return response()->json([
                'status' => false,
                'error' => [
                    'message' => 'School id is required in header',
                    'status_code' => 400
                ]
            ], 400);
        }
        session(['school_id' => $request->input('school_id')]);
        return $next($request);
    }
}
