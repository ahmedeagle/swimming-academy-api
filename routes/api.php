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
Route::group(['namespace' => 'Api', 'middleware' => ['CheckPassword', 'ChangeLanguage', 'api']], function () {
    Route::post('about-us', 'GeneralController@aboutUs')->name('academies.all');
    Route::post('events', 'EventController@events')->name('event.all');
    Route::post('activities', 'ActivityController@activities')->name('activities.all');
    Route::post('heroes', 'HeroController@heroes')->name('heroes.all');
    Route::group(['prefix' => 'academies'], function () {
        Route::post('/', 'AcademyController@getAcademies')->name('academies.all');
    });

    Route::group(['prefix' => 'coach', 'namespace' => 'Coach'], function () {
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

    Route::group(['prefix' => 'teams', 'namespace' => 'Team'], function () {
        Route::post('/', 'TeamController@getAllTeams')->name('team.all');
        Route::group(['middleware' => ['CheckCoachToken']], function () {
            Route::post('students', 'TeamController@getStudent')->name('team.students');
        });
    });

    Route::group(['prefix' => 'user', 'namespace' => 'User'], function () {
        Route::post('register', 'UserController@register')->name('user.register');
        Route::post('login', 'UserController@login')->name('user.login');
        Route::post('/forgetPassword', "UserController@forgetPassword");
        Route::post('/resend/verification-code', "UserController@resendCodeVerification");
        Route::post('/codeverification', "UserController@CodeVerification");
        Route::post('/resetPassword', "UserController@passwordReset");
        Route::group(['middleware' => ['CheckUserToken']], function () {
            Route::post('logout', 'UserController@logout')->name('user.logout');
            Route::group(['middleware' => ['CheckUserStatus']], function () {
                Route::post('profile/update', 'UserController@update_user_profile')->name('user.update.profile');
                Route::post('notifications', 'NotificationController@get_notifications')->name('user.notifications');
                Route::group(['prefix' => 'tickets'], function () {
                    Route::post('/', 'TicketController@getTickets')->name('user.tickets');
                    Route::post('new ', 'TicketController@newTicket')->name('user.add.ticket');
                    Route::post('AddMessage ', 'TicketController@AddMessage')->name('user.AddMessage');
                    Route::post('Messages', 'TicketController@GetTicketMessages')->name('user.GetTicketMessages');
                });
            });
        });
    });
});

