<?php

use Illuminate\Http\Request;

header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
/*
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
Route::group(['middleware' => ['CheckPassword','ChangeLanguage','api']], function () {

    Route::group(['prefix' => 'coach','namespace' => 'api\Coach'], function () {
        Route::post('login', 'CoachController@login')->name('coach.login');
        Route::post('register', 'CoachController@store')->name('coach.register');
        Route::post('reset/password', 'CoachController@resetPassword')->name('coach.password.reset');
        Route::post('/forgetPassword', "CoachController@forgetPassword");
        Route::post('rates', 'UserController@getProviderRate')->name('user.coach.rate');

        // provider which has token
        Route::group(['middleware' => ['CheckCoachToken']], function () {
            Route::post('logout', 'ProviderController@logout')->name('provider.logout');
            Route::post('PrepareUpdateProfile', 'ProviderController@prepare_update_provider_profile')->name('provider.edit.profile');
            Route::post('profile/update', 'ProviderController@update_provider_profile')->name('provider.update.profile');
        });
    });

});

