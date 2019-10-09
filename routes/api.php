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

    Route::group(['namespace' => 'Auth'], function () {
        Route::post('/register', 'RegisterController@register');
        Route::post('/register/organizer', 'RegisterController@registerOrganizer');
        Route::post('/login', 'LoginController@sendLoginResponse');
/*        Route::group(['middleware' => 'email.verified'], function () {
            Route::post('/login', 'LoginController@login');
        });*/

        /*        Route::group(['middleware' => 'email.verified'], function () {
                    Route::post('/login', 'LoginController@login');
                    Route::post('/password/email', 'ForgotPasswordController@getResetToken');
                    Route::post('/password/reset', 'ResetPasswordController@reset');
                });*/
    });
});

