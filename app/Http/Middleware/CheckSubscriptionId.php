<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Qs;

class CheckSubscriptionId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$allowedPlans)
    {
        $user = $request->user();
        if ($user && $user->organisation->subscription['id'] != 1) {
            $organisation = $user->organisation;
            if ($organisation->expiry_date && $organisation->expiry_date > now()) {
                if (in_array($organisation->subscription['id'], $allowedPlans)) {
                    return $next($request);
                } else {
                    return QS::respondWithError("Subscription plan does not allow access to this resource", 403);
                }
            }else{
                return QS::respondWithError("Subscription Expired", 403);
            }
        }
        return response()->json(['error' => 'Only paid subscription allow'], 403);
    }
}
