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
Route::group(['middleware' => ['CheckPassword', 'ChangeLanguage', 'api']], function () {

    Route::group(['prefix' => 'academies', 'namespace' => 'Api'], function () {
        Route::post('/', 'AcademyController@getAcademies')->name('academies.all');
    });

    Route::group(['prefix' => 'coach', 'namespace' => 'Api\Coach'], function () {
        Route::post('login', 'CoachController@login')->name('coach.login');

        // authenticated routes
        Route::group(['middleware' => ['CheckCoachToken']], function () {
            Route::post('logout', 'CoachController@logout')->name('coach.logout');
            Route::group(['middleware' => ['CheckCoachStatus']], function () {
                Route::post('teams', 'CoachController@teams')->name('coach.teams');
                Route::post('PrepareUpdateProfile', 'CoachController@prepare_update_coach_profile')->name('coach.edit.profile');
                Route::post('profile/update', 'CoachController@update_coach_profile')->name('coach.update.profile');
            });
        });
    });

    Route::group(['prefix' => 'teams', 'namespace' => 'Api\Team'], function () {
        Route::post('/', 'TeamController@getAllTeams')->name('team.all');
        Route::group(['middleware' => ['CheckCoachToken']], function () {
            Route::post('students', 'TeamController@getStudent')->name('team.students');
        });
    });

    Route::group(['prefix' => 'user', 'namespace' => 'Api\User'], function () {
        Route::post('register', 'UserController@register')->name('user.register');
        Route::post('login', 'UserController@login')->name('user.login');
        Route::post('/forgetPassword', "UserController@forgetPassword");
        Route::post('/resend/verification-code', "UserController@resendCodeVerification");
        Route::post('/codeverification', "UserController@CodeVerification");
        Route::post('/resetPassword', "UserController@passwordReset");
        Route::group(['middleware' => ['CheckUserToken']], function () {
            Route::post('logout', 'UserController@logout')->name('user.logout');
            Route::group(['middleware' => ['CheckUserStatus']], function () {
            });
        });
    });


});

