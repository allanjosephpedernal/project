<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// v1
Route::prefix('v1')->group(function (){
    // account
    Route::prefix('/account')->group(function (){
        Route::post('/invite', 'EmailVerificationController@invite')->name('v1.account.invite');
        Route::post('/create/{email}', 'EmailVerificationController@create')->name('v1.account.create');
        Route::post('/verify/{email}', 'EmailVerificationController@verify')->name('v1.account.verify');
    });

    // auth
    Route::prefix('/auth')->group(function(){
        Route::post('/login','AuthController@login')->name('v1.auth.login');
    });

    Route::middleware(['auth:sanctum'])->group(function(){
        // auth
        Route::prefix('/auth')->group(function(){
            Route::post('/logout','AuthController@logout')->name('v1.auth.logout');
        });

        // profile
        Route::prefix('/profile')->group(function(){
            Route::post('/{id}','ProfileController@update')->name('v1.profile.update');
        });
    });

});