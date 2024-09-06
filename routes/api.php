<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test2', function(){
    return "test2";
});

// Registration Route
Route::post('/register', [AuthController::class, 'register']);

// Login Route
Route::post('/login', [AuthController::class, 'login']);

// Route::middleware(['auth:api', 'check.subscription'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:api'])->group(function(){
    Route::middleware('check.subscription')->group(function(){
        Route::get('/user', function(Request $request) {
            return $request->user();
        });

        Route::get('/another-protected-route', function() {
            return "This route is protected by both auth and subscription check";
        });
    });
    // Route that not required subscription check
    Route::get('/test3', function(){
        return "This route not required any subscription";
    });
       Route::post('/holidays/store-update', [AttendanceController::class, 'storeOrUpdateHoliday']);
       Route::post('/attendance/add', [AttendanceController::class, 'store']);
       Route::get('/attendance', [AttendanceController::class, 'index']);
    // Route::post('/holidays/store-update', function(){
    //     return "RAMM";
    // });
});