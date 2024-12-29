<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserHomeController;
use App\Http\Controllers\Api\v1\StudentController;
use App\Http\Controllers\API\v1\AttendanceController;
use App\Http\Controllers\API\v1\PromotionController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\MyClassController;
use App\Http\Controllers\Api\v1\SubjectController;
use App\Http\Controllers\Api\v1\SectionController;
use App\Http\Controllers\Api\v1\OrganisationController;


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
        Route::get('/user', function(Request $request) {
            return $request->user();
        });
    });

    Route::group(['middleware' => ['auth:api', 'check.school.id']], function () {
            Route::post('userHome', 'UserHomeController@userHome');
            Route::get('get-schools', 'UserHomeController@getSchools');

            Route::post('sendPusgNotification', [StudentController::class, 'sendPusgNotification']);


            Route::post('logout', 'AuthController@logout');
        
        /************************ AJAX ****************************/
        Route::group(['prefix' => 'ajax'], function() {
            Route::get('get_lga/{state_id}', 'AjaxController@get_lga')->name('get_lga');
            Route::get('get_class_sections/{class_id}', 'AjaxController@get_class_sections')->name('get_class_sections');
            Route::get('get_class_subjects/{class_id}', 'AjaxController@get_class_subjects')->name('get_class_subjects');
        });
        /*************** Students *****************/
            
        Route::group(['prefix' => 'students'], function(){
			Route::post('reset_pass/{st_id}', [StudentController::class, 'reset_pass']);
			Route::get('graduated', [StudentController::class, 'graduated']);
			Route::put('not_graduated/{id}', [StudentController::class, 'not_graduated']);
			Route::get('listAllClass', [StudentController::class, 'listClass'])->middleware('teamSAT');
			Route::get('list/{class_id}', [StudentController::class, 'listByClass'])->middleware('teamSAT');
			Route::get('section/{section_id}', [StudentController::class, 'listBySection'])->middleware('teamSAT');
            
            Route::get('citys/{state_id}', [StudentController::class, 'citys']);
            Route::get('class_sections/{class_id}', [StudentController::class, 'classSections']);

			Route::get('student_details/{sr_id}', [StudentController::class, 'show']); //delete studentDetails method
			Route::get('edit/{sr_id}/{is_grad?}', [StudentController::class, 'edit']);
			Route::get('delete/{sr_id}/{is_grad?}', [StudentController::class, 'destroy']);
			Route::post('/update/{sr_id}/{is_grad?}', [StudentController::class, 'update']);            
            Route::post('/add', [StudentController::class, 'store']);
            
            Route::get('graduated/{sr_id}', [StudentController::class, 'show_graduate']);
            Route::get('view/{sr_id}/{is_grad?}', [StudentController::class, 'show']);
            

            Route::group(['prefix' => 'promotion'], function(){
                // Route::get('testr', [PromotionController::class, 'promotion']);
                Route::get('', 'PromotionController@promotion');
                /* Promotions */
                // Route::post('promote_selector', 'PromotionController@selector');
                Route::get('manage', 'PromotionController@manage');
                Route::delete('reset/{pid}', 'PromotionController@reset');
                Route::delete('reset_all', 'PromotionController@reset_all');
                Route::get('{fc?}/{fs?}/{tc?}/{ts?}', 'PromotionController@promotion');
                Route::post('{fc}/{fs}/{tc}/{ts}', 'PromotionController@promote');
            });

        });
        /*************** Users *****************/
        Route::group(['prefix' => 'user'], function(){
            Route::post('get_user_create', [UserController::class, 'get_user_create']);
            Route::get('get_user_types', [UserController::class, 'get_user_types']);
            Route::post('get_users_by_types', [UserController::class, 'get_usersByTypes']);


            Route::post('save_user', [UserController::class, 'store']);
            Route::put('save_user/{user_hash}', [UserController::class, 'update']);
            Route::post('reset_pass/{user_id}', [UserController::class, 'reset_pass']);
            Route::delete('destroy/{user_hash}', [UserController::class, 'destroy']);

            Route::get('show/{user_hash}', [UserController::class, 'show']);
            
        });

        /**************Manage Classes************* */
        Route::group(['prefix' => 'manage_classes'], function(){
            // Route::resource('classes', 'MyClassController');

            Route::get('show', [MyClassController::class, 'show']);
            Route::post('create', [MyClassController::class, 'store']);
            Route::get('{class}/edit', [MyClassController::class, 'edit']);
            Route::put('update/{class}', [MyClassController::class, 'update']);
            Route::delete('destroy/{class}', [MyClassController::class, 'destroy']);
        });

        /**************Manage Subjects************* */
        Route::group(['prefix' => 'manage_subjects'], function(){
            // Route::resource('subjects', 'SubjectController');

            Route::get('show', [SubjectController::class, 'show']);
            Route::post('create', [SubjectController::class, 'store']);
            Route::get('all-subjects/{class_id}', [SubjectController::class, 'allSubjects']);
            Route::get('{class}/edit', [SubjectController::class, 'edit']);
            Route::put('update/{class}', [SubjectController::class, 'update']);
            Route::delete('destroy/{class}', [SubjectController::class, 'destroy']);
        });

        /**************Manage Sections************* */
        Route::group(['prefix' => 'manage_sections'], function(){
            Route::resource('sections', 'SectionController');

            Route::get('getClassSections/{class_id}', [SectionController::class, 'getClassSections']);
            
        });
        
        /**************Manage Droms************* */
        Route::group(['prefix' => 'manage_dorms'], function(){
            Route::resource('dorms', 'DormController');
        });

        /*************** TimeTables *****************/
        Route::group(['prefix' => 'timetables'], function(){
            Route::get('/', 'TimeTableController@index')->name('tt.index');
            Route::get('/{class_id}', 'TimeTableController@time_table_by_class')->name('tt.time_table_by_class');

            Route::group(['middleware' => 'teamSA'], function() {
                Route::post('/', 'TimeTableController@store')->name('tt.store');
                Route::put('/{tt}', 'TimeTableController@update')->name('tt.update');
                Route::delete('/{tt}', 'TimeTableController@delete')->name('tt.delete');
            });

            /*************** TimeTable Records *****************/
            Route::group(['prefix' => 'records'], function(){

                Route::group(['middleware' => 'teamSA'], function(){
                    Route::get('manage/{ttr}', 'TimeTableController@manage')->name('ttr.manage');
                    Route::post('/', 'TimeTableController@store_record')->name('ttr.store');
                    Route::get('edit/{ttr}', 'TimeTableController@edit_record')->name('ttr.edit');
                    Route::put('/{ttr}', 'TimeTableController@update_record')->name('ttr.update');
                });

                Route::get('show/{ttr}', 'TimeTableController@show_record')->name('ttr.show');
                Route::get('print/{ttr}', 'TimeTableController@print_record')->name('ttr.print');
                Route::delete('/{ttr}', 'TimeTableController@delete_record')->name('ttr.destroy');

            });

            /*************** Time Slots *****************/
            Route::group(['prefix' => 'time_slots', 'middleware' => 'teamSA'], function(){
                Route::post('/', 'TimeTableController@store_time_slot')->name('ts.store');
                Route::post('/use/{ttr}', 'TimeTableController@use_time_slot')->name('ts.use');
                Route::get('edit/{ts}', 'TimeTableController@edit_time_slot')->name('ts.edit');
                Route::delete('/{ts}', 'TimeTableController@delete_time_slot')->name('ts.destroy');
                Route::put('/{ts}', 'TimeTableController@update_time_slot')->name('ts.update');
            });

        });

        //Susbcription Table
        // 1 - Free // 2 - Gold // 3 - Diamond    
        Route::middleware('check.subscription:2,3')->group(function(){
            // Route::get('/user', function(Request $request) {
            //     $userRepo = new UserRepo();
            //     return $userRepo->getAll();
            // });


            // Route::get('/user', function(Request $request) {
            //     return $request->user();
            // });

            Route::get('/another-protected-route', function() {
                return "This route is protected by both auth and subscription check";
            });
        });


        // Route that not required subscription check
        Route::get('/test3', [OrganisationController::class, 'index']);
        Route::get('/org/{org_id?}', [OrganisationController::class, 'index']);
        Route::post('/org', [OrganisationController::class, 'store']);
        Route::put('/org/{org_id}', [OrganisationController::class, 'update']);
        Route::delete('/org/delete/{org_id}', [OrganisationController::class, 'deleteOrg']);

        Route::get('/school/{school_id?}', [OrganisationController::class, 'school_index']);
        Route::post('/school', [OrganisationController::class, 'school_store']);
        Route::put('/school/{school_id}', [OrganisationController::class, 'school_update']);
        Route::delete('/school/delete/{school_id}', [OrganisationController::class, 'deleteSchool']);

        Route::post('/holidays/store-update', [AttendanceController::class, 'storeOrUpdateHoliday']);
        Route::post('/attendance/add', [AttendanceController::class, 'store']);
        Route::get('/attendance', [AttendanceController::class, 'index']);
    //    Route::post('/attendance', [AttendanceController::class, 'index']);
    });
});