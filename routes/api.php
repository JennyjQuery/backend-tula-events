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

    //UNAUTHORIZED USER
    Route::group(['namespace' => 'Auth'], function () {

        Route::post('/register', 'RegisterController@register');
        Route::post('/register/organizer', 'RegisterController@registerOrganizer');
        Route::post('/login', 'LoginController@sendLoginResponse');

        Route::get('/event/{date_from?}/{date_to?}/{type?}/{place?}/{near_me?}/{page}', 'UnAuthUserController@getEvents');
        Route::get('/event/map/{place}/{page}', 'UnAuthUserController@getEventsOnMap');
        Route::get('/event/{id}', 'UnAuthController@getMoreInformation');
        Route::put('/event/search/{name?}/{page}', 'UnAuthController@searchEvent');

        Route::group(['middleware' => 'email.verified'], function () {
            Route::post('/login', 'LoginController@login');
            Route::post('/password/email', 'ForgotPasswordController@getResetToken');
            Route::post('/password/reset', 'ResetPasswordController@reset');
        });
    });

    //USER
    Route::group(['middleware' => 'auth:api'], function () {

        //User profile
        Route::get('/user/event/{past?}/{page}', 'AuthUserController@getEvent');
        Route::put('/review/like', 'ReviewController@putLike');
        Route::get('/user', 'AuthUserController@getProfile');
        Route::get('/user/update', 'AuthUserController@updateProfile');

        //ORGANIZER
        Route::group(['middleware' => 'role:organizer'], function () {
            //Organizer event
            Route::post('/user/event', 'EventController@createEvent');
            Route::put('/user/event/update/{id}', 'EventController@updateEvent');
        });

        //PARTICIPANT
        Route::group(['middleware' => 'role:participant'], function () {
            //Participant event
            Route::put('/user/event/status', 'EventController@updateStatusEvent');
            Route::put('/user/review', 'ReviewController@addEventFeedback');
        });
    });

});
