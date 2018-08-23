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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::get('overall-revisions', 'DashboardController@overallRevisions');
Route::get('monthly-revisions', 'DashboardController@revisionsPerMonth');
Route::get('alerts', 'DashboardController@alerts');

Route::middleware(['auth:api'])->group(function () {
    Route::get('point', 'PointController@getNearestPoint');
    Route::get('points', 'PointController@index');
    Route::match(['get', 'post'], 'botman', 'BotManController@handle');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
