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

Route::group(['namespace' => 'Admin', 'middleware' => ['guest:admin']], function () {

    Route::get("/login", "LoginController@get_login")->name('admin.login');
    Route::post("/login", "LoginController@post_login");
    Route::get("/forget-password", "ForgetPasswordController@get_forget_password")->name('admin.get.forgetpassword');
    Route::post("/forget-password", "ForgetPasswordController@post_forget_password")->name('admin.post.forgetpassword');
    Route::get("/confirm-code/{code?}", "ForgetPasswordController@get_code_confirmation")->name('admin.get.codeconfirmation');
    Route::post("/confirm-code", "ForgetPasswordController@confirmCode")->name('admin.confirmcode');
    Route::get("/password-reset/{activation_code}", "ForgetPasswordController@get_password_reset")->name('admin.get.passwordreset');
    Route::post("/confirm-reset", "ForgetPasswordController@password_reset")->name('admin.post.passwordreset');
});

Route::group(['namespace' => 'Admin', 'middleware' => ['auth:admin']], function () {


    Route::get('test', function () {
        getDaysInMonth(1, 2020);

    });

    Route::get("/", "DashboardController@dashboard")->name('admin.dashboard');

    Route::group(['prefix' => 'categories'], function () {
        Route::get("/", "CategoryController@index")->name('admin.categories.all');
        Route::get("/create", "CategoryController@create")->name('admin.categories.create');
        Route::post("/store", "CategoryController@store")->name('admin.categories.store');
        Route::get("/edit/{id}", "CategoryController@edit")->name('admin.categories.edit');
        Route::post("/update/{id}", "CategoryController@update")->name('admin.categories.update');
        Route::post("/loadcategories", "CategoryController@loadCategories")->name('admin.categories.loadCategories');
        Route::post('/teams', 'CategoryController@loadCategoryTeams')->name('admin.categories.loadTeams');
        Route::get('/delete/{id}', 'CategoryController@deleteCategory')->name('admin.categories.delete');
        Route::post("/loadHeroes", "CategoryController@loadHeroes")->name('admin.categories.loadHeroes');
    });

    Route::group(['prefix' => 'academies'], function () {
        Route::get("/", "AcademyController@index")->name('admin.academies.all');
        Route::get("/create", "AcademyController@create")->name('admin.academies.create');
        Route::post("/store", "AcademyController@store")->name('admin.academies.store');
        Route::get("/edit/{id}", "AcademyController@edit")->name('admin.academies.edit');
        Route::post("/update/{id}", "AcademyController@update")->name('admin.academies.update');
        Route::post('/teams', 'AcademyController@loadAcademyHeroes')->name('admin.academies.heroes');
        Route::get('/delete/{id}', 'AcademyController@deleteAcademy')->name('admin.academies.delete');
        Route::get('/about-us/{id}', 'AcademyController@academyAboutUs')->name('admin.academies.aboutus');
        Route::post('/about-us', 'AcademyController@saveAboutUs')->name('admin.academies.postaboutus');
    });

    Route::group(['prefix' => 'teams'], function () {
        Route::get("/", "TeamController@index")->name('admin.teams.all');
        Route::get("/create", "TeamController@create")->name('admin.teams.create');
        Route::post("/store", "TeamController@store")->name('admin.teams.store');
        Route::get("/edit/{id}", "TeamController@edit")->name('admin.teams.edit');
        Route::post("/update/{id}", "TeamController@update")->name('admin.teams.update');
        Route::get("/working-days/{id}", "TeamController@getWorkingDay")->name('admin.teams.days');
        Route::post("/working-days", "TeamController@saveWorkingDay")->name('admin.teams.postworkingdays');
        Route::get("/coaches/{id}", "TeamController@getTeamCoaches")->name('admin.teams.coaches');
        Route::get("/users/{id}", "TeamController@getTeamStudents")->name('admin.teams.users');
        Route::post("/loadHeroes", "TeamController@loadHeroes")->name('admin.teams.loadHeroes');
        Route::get('/delete/{id}', 'TeamController@deleteTeam')->name('admin.teams.delete');
    });


    Route::group(['prefix' => 'profile'], function () {
        Route::get("/", "ProfileController@edit")->name('admin.profile.edit');
        Route::post("/", "ProfileController@update")->name('admin.profile.update');
    });

    Route::group(['prefix' => 'coaches'], function () {
        Route::get("/", "CoachController@index")->name('admin.coaches.all');
        Route::get("/create", "CoachController@create")->name('admin.coaches.create');
        Route::post("/store", "CoachController@store")->name('admin.coaches.store');
        Route::get("/edit/{id}", "CoachController@edit")->name('admin.coaches.edit');
        Route::post("/update/{id}", "CoachController@update")->name('admin.coaches.update');
        Route::get("/users/{id}", "CoachController@getCoachStudents")->name('admin.coaches.users');
        Route::get("/teams/{id}", "CoachController@teams")->name('admin.coaches.teams');
        Route::get('/delete/{id}', 'CoachController@deleteCoach')->name('admin.coaches.delete');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get("/", "UserController@index")->name('admin.users.all');
        Route::get("/create", "UserController@create")->name('admin.users.create');
        Route::post("/store", "UserController@store")->name('admin.users.store');
        Route::get("/edit/{id}", "UserController@edit")->name('admin.users.edit');
        Route::get("/details/{id}", "UserController@edit")->name('admin.users.details');
        Route::post("/update/{id}", "UserController@update")->name('admin.users.update');
        Route::get('/delete/{id}', 'UserController@deleteUser')->name('admin.users.delete');

        Route::group(['prefix' => 'tickets'], function () {
            Route::get('/', 'UserMessageController@index')->name('admin.users.tickets.all');
            Route::get('/delete/{id}', 'UserMessageController@destroy')->name('admin.users.tickets.delete');
            Route::get('/solved/{id}', 'ProviderMessageController@solvedMessage')->name('admin.user.tickets.solved');
            Route::get('/show/{id}', 'UserMessageController@view')->name('admin.users.tickets.view');
            Route::get('/reply/{id}', 'UserMessageController@getReply')->name('admin.users.tickets.getreply');
            Route::post('/reply', 'UserMessageController@reply')->name('admin.users.tickets.reply');
        });
    });

    Route::group(['prefix' => 'subscriptions'], function () {
        Route::get("/", "SubscriptionController@subscriptions")->name('admin.subscriptions');
    });

    Route::group(['prefix' => 'events'], function () {
        Route::get("/", "EventController@index")->name('admin.events.all');
        Route::get("/create", "EventController@create")->name('admin.events.create');
        Route::post("/store", "EventController@store")->name('admin.events.store');
        Route::post("/savetImages", "EventController@storeEventImages")->name('admin.events.storeImages');
        Route::get("/edit/{id}", "EventController@edit")->name('admin.events.edit');
        Route::post("/update/{id}", "EventController@update")->name('admin.events.update');
        Route::get("/delete/{id}", "EventController@delete")->name('admin.events.delete');
    });

    Route::group(['prefix' => 'activities'], function () {
        Route::get("/", "ActivityController@index")->name('admin.activities.all');
        Route::get("/create", "ActivityController@create")->name('admin.activities.create');
        Route::post("/store", "ActivityController@store")->name('admin.activities.store');
        Route::get("/edit/{id}", "ActivityController@edit")->name('admin.activities.edit');
        Route::post("/update/{id}", "ActivityController@update")->name('admin.activities.update');
        Route::get("/delete/{id}", "ActivityController@delete")->name('admin.activities.delete');
    });

    Route::group(['prefix' => 'heroes'], function () {
        Route::get("/", "HeroController@index")->name('admin.heroes.all');
        Route::get("/currentWeek", "HeroController@currentWeek")->name('admin.heroes.currentWeek');
        Route::get("/create", "HeroController@create")->name('admin.heroes.create');
        Route::post("/store", "HeroController@store")->name('admin.heroes.store');
        Route::get("/edit/{id}", "HeroController@edit")->name('admin.heroes.edit');
        Route::post("/update/{id}", "HeroController@update")->name('admin.heroes.update');
        Route::get("/delete/{id}", "HeroController@delete")->name('admin.heroes.delete');
        Route::get("/note", "HeroController@addHeroNote")->name('admin.heroes.note');


    });


    Route::group(['prefix' => 'champions'], function () {
        Route::get("/", "ChampionController@index")->name('admin.champions.all');
        Route::get("/create", "ChampionController@create")->name('admin.champions.create');
        Route::post("/store", "ChampionController@store")->name('admin.champions.store');
        Route::get("/edit/{id}", "ChampionController@edit")->name('admin.champions.edit');
        Route::post("/update/{id}", "ChampionController@update")->name('admin.champions.update');
        Route::get("/delete/{id}", "ChampionController@delete")->name('admin.champions.delete');
        Route::get("/note", "ChampionController@addChampionNote")->name('admin.champions.note');
    });

    Route::get("/logout", "LoginController@logout")->name('admin.logout');
});
