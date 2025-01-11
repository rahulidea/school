<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckSchoolFilled
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
        $user = $request->user();
        $schoolSetting = Setting::where('school_id',$user->school_id)->whereIn('type',['system_name', 'phone', 'address'])->get();

        foreach ($schoolSetting as $setting) {
            if (empty($setting->description)) {
                if ($request->expectsJson()){
                    return response()->json([
                        'status' => false,
                        'error' => [
                            'message' => 'Please complete all required fields in your profile.',
                            'error_code' => '10001',
                            'status_code' => 400
                        ]
                    ], 400);
                }else{
                    return redirect()->route('settings')->with('pop_warning', 'Please complete all required fields in Setting.');
                }
            }
        }

        return $next($request);
    }
}
