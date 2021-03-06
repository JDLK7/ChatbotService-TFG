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

Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::view('/import', 'import')->name('import');
    Route::post('/import', 'PointController@import');
    
    Route::get('/home', 'HomeController@index')->name('home');
    
    Route::match(['get', 'post'], '/botman', 'BotManController@handle');
    Route::get('/botman/tinker', 'BotManController@tinker');

    // Rutas del panel de control
    Route::get('points/{point}', 'PointController@show');
    Route::get('alerts', 'DashboardController@alerts');
    Route::get('overall-revisions', 'DashboardController@overallRevisions');
    Route::get('points-by-alert-type', 'PointController@pointsByAlertType');
    Route::get('monthly-revisions', 'DashboardController@revisionsPerMonth');

});
