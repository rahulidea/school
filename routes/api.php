<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserHomeController;
use App\Http\Controllers\Api\v1\StudentController;
use App\Http\Controllers\API\v1\AttendanceController;


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

Route::middleware(['auth:api', 'check.subscription'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
    Route::group(['middleware' => ['guest']], function () {
        // Registration Route
        Route::post('/register', [AuthController::class, 'register']);

        // Login Route
        Route::post('/login', [AuthController::class, 'login']);

        // Password Reset
        // Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail');
    });

    Route::group(['middleware' => ['auth:api']], function () {
            Route::post('userHome', 'UserHomeController@userHome');

            Route::post('logout', 'AuthController@logout');
            Route::get('/user', function (Request $request) {
                return $request->user();
            });


            /*************** Students *****************/
        Route::group(['prefix' => 'students'], function(){
			Route::get('testr', [StudentController::class, 'test']);
			Route::get('reset_pass/{st_id}', [StudentController::class, 'reset_pass']);
			Route::get('graduated', [StudentController::class, 'graduated']);
			Route::put('not_graduated/{id}', [StudentController::class, 'not_graduated']);
			Route::get('listAllClass', [StudentController::class, 'listClass'])->middleware('teamSAT');
			Route::get('list/{class_id}', [StudentController::class, 'listByClass'])->middleware('teamSAT');

			Route::get('student_details/{sr_id}', [StudentController::class, 'studentDetails']);
			Route::get('edit/{sr_id}', [StudentController::class, 'edit']);
			Route::post('/update/{sr_id}', [StudentController::class, 'update']);
            /* Promotions */
            /*Route::post('promote_selector', 'PromotionController@selector');
            Route::get('promotion/manage', 'PromotionController@manage');
            Route::delete('promotion/reset/{pid}', 'PromotionController@reset');
            Route::delete('promotion/reset_all', 'PromotionController@reset_all');
            Route::get('promotion/{fc?}/{fs?}/{tc?}/{ts?}', 'PromotionController@promotion');
            Route::post('promote/{fc}/{fs}/{tc}/{ts}', 'PromotionController@promote');*/

        });
            
        Route::middleware('check.subscription')->group(function(){
            Route::get('/user', function(Request $request) {
                return $request->user();
            });

            Route::get('/another-protected-route', function() {
                return "This route is protected by both auth and subscription check";
            });
        });
        // Route that not required subscription check
        Route::post('/test3', function(){
            return "This route not required any subscription";
        });
        Route::post('/holidays/store-update', [AttendanceController::class, 'storeOrUpdateHoliday']);
        Route::post('/attendance/add', [AttendanceController::class, 'store']);
        Route::get('/attendance', [AttendanceController::class, 'index']);
    //    Route::post('/attendance', [AttendanceController::class, 'index']);
    });
});