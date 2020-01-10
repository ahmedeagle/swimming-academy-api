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
Route::group(['middleware' => ['CheckPassword', 'api']], function () {


    Route::post('settings', 'GlobalController@settings')->name('settings');

    Route::post('subscriptions', 'GlobalController@subscriptions')->name('subscriptions');
    Route::post('tickets', 'ProviderController@getTickets')->name('provider.tickets');
    Route::post('new/ticket ', 'ProviderController@newTicket')->name('provider.add.ticket');
    Route::post('AddMessage ', 'ProviderController@AddMessage')->name('provider.AddMessage');
    Route::post('GetTicketMessages', 'ProviderController@GetTicketMessages')->name('provider.GetTicketMessages');
    Route::post('categories', 'GlobalController@getCategories')->name('categories');
    Route::post('cities', 'CityController@index')->name('cities');
    Route::post('agreement', 'GlobalController@getAgreement')->name('agreement');;
    Route::post('app/data', 'GlobalController@getAppData')->name('app.data');

    // User routes
    Route::group(['prefix' => 'user'], function () {

        Route::post('register', 'UserController@store')->name('user.register');
        Route::post('login', 'UserController@login')->name('user.login');
        Route::post('activate/account', 'UserController@activateAccount')->name('user.activate.account');
        Route::post('resend/activation', 'UserController@resendActivation')->name('user.resend.activation');
        // user which authenticated
        Route::group(['middleware' => 'CheckUserToken'], function () {
            Route::post('logout', 'UserController@logout')->name('user.logout');
        });

        // user which subscribe  and authenticated
        Route::group(['middleware' => ['CheckUserStatus', 'CheckUserToken']], function () {
            Route::post('rate', 'UserController@userRating')->name('user.rate');
            Route::post('provider/rates', 'UserController@getProviderRate')->name('user.provider.rate');
        });
    });

    Route::group(['prefix' => 'provider'], function () {

        Route::post('login', 'ProviderController@login')->name('provider.login');
        Route::post('register', 'ProviderController@store')->name('provider.register');
        Route::post('activate/account', 'ProviderController@activateAccount')->name('provider.activate.account');
        Route::post('reset/password', 'ProviderController@resetPassword')->name('provider.password.reset');
        Route::post('/forgetPassword', "ProviderController@forgetPassword");
        Route::post('rates', 'UserController@getProviderRate')->name('user.provider.rate');
        Route::group(['middleware' => 'CheckProviderToken'], function () {
            Route::post('resend/activation', 'ProviderController@resendActivation')->name('provider.resend.activation');
        });

        // provider which has token
        Route::group(['middleware' => ['CheckProviderToken', 'CheckProviderStatus']], function () {
            Route::post('logout', 'ProviderController@logout')->name('provider.logout');
            Route::post('PrepareUpdateProfile', 'ProviderController@prepare_update_provider_profile')->name('provider.edit.profile');
            Route::post('profile/update', 'ProviderController@update_provider_profile')->name('provider.update.profile');
        });
    });
});

