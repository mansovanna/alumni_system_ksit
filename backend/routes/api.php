<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\EventJoinController;
use App\Http\Controllers\API\MajorController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return response()->json(
        [
            'message' => 'Hey you Test API?'
        ]
    );
});



Route::prefix('v1')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);

    // get major for user complete data major
    Route::get('/depament-major', [MajorController::class, 'getAll']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [UserController::class, 'getMe']);
        Route::delete('logout', [UserController::class, 'delete']);
        // Block Update profile images
        Route::put('update-image', [UserController::class, 'updateImage']);
        Route::put('update-info', [UserController::class, 'updateInfo']);
          Route::put('reset-password/{id}', [AdminController::class, 'restPass']);
        // Block routes for admin
        Route::prefix('admin')->name('name.')->group(function () {
            // Block dashboard route
            Route::get('/dashboard', [DashboardController::class, 'index']);

            // Chart Graph
            Route::get('chart-by-work-status', [DashboardController::class, 'chartByStatusWork']);
            Route::get('chart-by-year', [DashboardController::class, 'chartByYear']);

            // Block any routes
            Route::resource('admin', AdminController::class);


            // -------------------------------------------------
            Route::resource('/major', MajorController::class);

            Route::resource('/user', UserController::class);

            Route::put('user-status/{id}', [UserController::class, 'updateStatus']);

            Route::resource('message', MessageController::class);

            Route::resource('event', EventController::class);

            Route::resource('event-join', EventJoinController::class);
        });

        // Block for user routes
        Route::prefix('user')->name('.name')->group(function () {
            //
        });
    });

});
