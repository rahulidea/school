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
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->organisation->subscription['id'] != 1) {
            $organisation = $request->user()->organisation;
            if ($organisation->expiry_date && $organisation->expiry_date > now()) {
                return $next($request);
            }else{
            //    return response()->json(['error' => 'Subscription Expired'], 403);
                return QS::respondWithError("Subscription Expired", 403);//->respondWithError($message);
            }
        }

        return response()->json(['error' => 'Only paid subscription allow'], 403);
    }
}
