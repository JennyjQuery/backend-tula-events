<?php

use Illuminate\Http\Request;

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

$middleware = '';
if (!App::runningUnitTests()) {
    $middleware = 'throttle:60,1';
}

Route::group(['middleware' => $middleware], function () {
    //UNAUTHUSER
    Route::get('/event/{date_from?}/{date_to?}/{type?}/{place?}/{near_me?}/{page}', 'UserController@getEventsOnMaimPage');//только запланированные и autorization=0
    Route::get('/event/map/{place}/{page}', 'EventController@getEventsOnMap');
                //MORE ABOUT EVENTS
    Route::get('/event/{id}', 'EventController@getMoreInformation');//да?????????????
    Route::put('/event/search/{name?}/{page}', 'EventController@searchEvent');

    //UNAUTHORIZED USER
    Route::group(['namespace' => 'Auth'], function () {

        Route::post('/register', 'RegisterController@register');//да
        Route::post('/register/organizer', 'RegisterController@registerOrganizer');//да
        Route::post('/login', 'LoginController@sendLoginResponse');//да

        Route::group(['middleware' => 'email.verified'], function () {
            Route::post('/login', 'LoginController@login');
            Route::post('/password/email', 'ForgotPasswordController@getResetToken');
            Route::post('/password/reset', 'ResetPasswordController@reset');
        });
    });
    //AUTHUSER
    Route::group(['middleware' => 'auth:api'], function () {

        //User profile
        // Route::get('/user/event/{past?}/{page}', 'EventController@getEvent');

        Route::get('/user/event', 'UserController@getEvents');//да

        Route::put('/review/like', 'ReviewController@putLike');
        Route::get('/user', 'UserController@getProfile');//да
        Route::put('/user/update', 'UserController@updateProfile');//валидировать только измененные поля//изменение email и телефона//пароль сохраняется без шифровки

        //ORGANIZER
        Route::group(['middleware' => 'role:organizer'], function () {
            //Organizer event
            Route::post('/user/create', 'EventController@createEvent');//да
            Route::put('/user/event/update/{id}', 'EventController@updateEvent');//да
            Route::get('/event/{id}/statistic', 'EventController@statisticEvent');//да
        });

        //PARTICIPANT
        Route::group(['middleware' => 'role:participant'], function () {
            //Participant event

            Route::put('/user/event/{id}/status', 'EventController@createStatusEvent');//да
            Route::put('/user/event/{id}/review', 'ReviewController@createReview');//да
        });
    });

});
