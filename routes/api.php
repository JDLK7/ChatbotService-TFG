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

Route::post('auth/login', 'AuthController@login');

Route::group([
    'middleware' => 'jwt.auth',
    'prefix' => 'auth'
], function ($router) {
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('point', 'PointController@getNearestPoint');
    Route::get('points', 'PointController@index');
    Route::match(['get', 'post'], 'botman', 'BotManController@handle');
});

Route::middleware('jwt.auth')->get('/user', function (Request $request) {
    return $request->user();
});
