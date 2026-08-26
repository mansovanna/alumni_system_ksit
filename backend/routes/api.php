<?php

use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AlumniIndexController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventJoinController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
|
| Alumni Management System API
|
*/

/*
|--------------------------------------------------------------------------
| API Test
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Alumni Management System API',
        'version' => 'v1',
    ]);
});


/*
|--------------------------------------------------------------------------
| API V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/login', [
        UserController::class,
        'login',
    ]);

    Route::post('/register', [
        UserController::class,
        'register',
    ]);


    Route::get('major-items', [MajorController::class, 'majorItems']);

    /*
    |--------------------------------------------------------------------------
    | Authenticated Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        Route::get('/me', [
            UserController::class,
            'me',
        ]);

        Route::post('/logout', [
            UserController::class,
            'logout',
        ]);

        Route::post('/logout-all', [
            UserController::class,
            'logoutAll',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Admin Only
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:admin')
            ->prefix('admin')
            ->group(function () {

                /*
                |------------------------------------------------------------------
                | Dashboard
                |------------------------------------------------------------------
                */

                Route::get('/dashboard', [
                    DashboardController::class,
                    'index',
                ]);


                /*
                |------------------------------------------------------------------
                | Staff Management
                |------------------------------------------------------------------
                */

                Route::apiResource('staff', AdminController::class);

                Route::put('reset-password/{id}', [AdminController::class, 'restPass']);

                Route::delete('delete-user/{id}', [UserController::class, 'destroy']);
                /*
                |------------------------------------------------------------------
                | User Management
                |------------------------------------------------------------------
                */

                Route::apiResource('users', UserController::class);


                /*
                |------------------------------------------------------------------
                | Settings
                |------------------------------------------------------------------
                */

                Route::get('/settings', [
                    SettingController::class,
                    'index',
                ]);

                Route::get('/settings/{key}', [
                    SettingController::class,
                    'show',
                ]);

                Route::put('/settings/{key}', [
                    SettingController::class,
                    'update',
                ]);


                /*
                |------------------------------------------------------------------
                | Activity Logs
                |------------------------------------------------------------------
                */

                Route::get('/activity-logs', [
                    ActivityLogController::class,
                    'index',
                ]);

                Route::get('/activity-logs/{id}', [
                    ActivityLogController::class,
                    'show',
                ]);
            });


        /*
        |--------------------------------------------------------------------------
        | Admin + Staff
        |--------------------------------------------------------------------------
        */
        Route::middleware([
            'auth:sanctum',
            'role:admin|staff',
        ])->group(function () {

            Route::get('/dashboard', [
                DashboardController::class,
                'index',
            ]);


            Route::get('/reports/employment', [ReportController::class, 'employmentReport']);


            /*
            |------------------------------------------------------------------
            | Alumni
            |------------------------------------------------------------------
            */

            Route::get('/alumni', [
                UserController::class,
                'alumniIndex',
            ]);

            Route::get('/alumni/{id}', [
                UserController::class,
                'alumniShow',
            ]);

            Route::post('/alumni', [
                UserController::class,
                'alumniStore',
            ]);

            Route::put('/alumni/{id}', [
                UserController::class,
                'alumniUpdate',
            ]);

            Route::put('/alumni-update-state/{id}', [
                UserController::class,
                'alumniUpdateState',
            ]);

            Route::delete('/alumni/{id}', [
                UserController::class,
                'alumniDestroy',
            ]);


            /*
            |------------------------------------------------------------------
            | Majors
            |------------------------------------------------------------------
            */

            Route::apiResource('majors', MajorController::class);


            /*
            |------------------------------------------------------------------
            | Events
            |------------------------------------------------------------------
            */

            Route::apiResource('events', EventController::class);


            /*
            |------------------------------------------------------------------
            | Event Registrations
            |------------------------------------------------------------------
            */

            Route::get('/events/{event}/registrations', [
                EventJoinController::class,
                'index',
            ]);

            Route::get('/events/{event}/registrations/{registration}', [
                EventJoinController::class,
                'show',
            ]);

            Route::post('/events/{event}/registrations', [
                EventJoinController::class,
                'store',
            ]);

            Route::put('/events/{event}/registrations/{registration}', [
                EventJoinController::class,
                'update',
            ]);

            Route::delete('/events/{event}/registrations/{registration}', [
                EventJoinController::class,
                'destroy',
            ]);


            /*
            |------------------------------------------------------------------
            | News / Announcements
            |------------------------------------------------------------------
            */

            Route::apiResource('news', NewsController::class);


            /*
            |------------------------------------------------------------------
            | Messages
            |------------------------------------------------------------------
            */

            Route::apiResource('messages', MessageController::class);


            /*
            |------------------------------------------------------------------
            | Notifications
            |------------------------------------------------------------------
            */

            Route::get('/notifications', [
                NotificationController::class,
                'index',
            ]);

            Route::get('/notifications/{id}', [
                NotificationController::class,
                'show',
            ]);

            Route::put('/notifications/{id}/read', [
                NotificationController::class,
                'markAsRead',
            ]);

            Route::put('/notifications/read-all', [
                NotificationController::class,
                'markAllAsRead',
            ]);

            Route::delete('/notifications/{id}', [
                NotificationController::class,
                'destroy',
            ]);
        });



        /*
        |--------------------------------------------------------------------------
        | Alumni or user general Routes
        |--------------------------------------------------------------------------
        */
        Route::middleware('role:alumni')
            ->prefix('alumnis')
            ->group(function () {

                // Dashboard
                Route::get('/', [
                    AlumniIndexController::class,
                    'index'
                ]);

                // Career Status
                Route::put('/update-status/{id}', [
                    AlumniIndexController::class,
                    'updateStatus'
                ]);

                // Events

                 Route::get('/events', [
                    AlumniIndexController::class,
                    'eventListAll'
                ]);


                Route::get('/events/{id}', [
                    AlumniIndexController::class,
                    'eventDetails'
                ]);

                Route::post('/events/{id}/register', [
                    AlumniIndexController::class,
                    'eventRegister'
                ]);

                // Directory
                Route::get('/directory', [
                    AlumniIndexController::class,
                    'alumniDirectory'
                ]);



                // Notifications
                Route::get('/notification/{id}', [
                    AlumniIndexController::class,
                    'notificationDetails'
                ]);


                // Profile
                Route::get('/profile', [AlumniIndexController::class, 'profile']);
            });
    });
});
