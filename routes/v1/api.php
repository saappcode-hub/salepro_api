<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\User\UserController;
use App\Http\Controllers\V1\GPSTracking\GPSTrackingController;

Route::group(
    [ 'prefix' => 'auth' ], 
    function () {
        Route::post('login',[AuthController::class,'login']);
    }
);

Route::group(
    [ 'middleware' => 'auth:sanctum' ],
    function(){

        /// User
        Route::group(['prefix' => 'user'], function () {
            Route::get('profile', [UserController::class, 'profile']);
        });

        /// GPS Tracking
        Route::post('gps-tracking', [GPSTrackingController::class, 'tracking']);
    }
);    