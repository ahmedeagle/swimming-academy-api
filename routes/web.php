<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {

    return view('welcome');
});


Route::get('test', function () {
    event(new App\Events\NewMessage('Someone'));
    return "Event has been sent!";
});


//will move to api routes with authenticatio
//user send maessage

Route::group(['namespace' => 'Admin'],function (){
    Route::post('new/ticket', 'UserMessageController@newTicket')->name('user.add.ticket');
    Route::post('AddMessage ', 'UserMessageController@AddMessage')->name('user.AddMessage');
});




