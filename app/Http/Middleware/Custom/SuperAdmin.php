<?php

namespace App\Http\Middleware\Custom;

use Closure;
use App\Helpers\Qs;
use PhpParser\Node\Stmt\Else_;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check() && Qs::userIsSuperAdmin())
            return $next($request);
        else
            if ($request->expectsJson())
                throw new HttpResponseException(response()->json(['status'=>false,'message' => 'Permission not allowed.'], 401));
        
        return redirect()->route('login');
    }
}
