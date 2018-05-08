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
    return view('index');
});

Route::get('/sobre', function () {
    return view('about');
});

Route::get('/informacoes', function () {
    return view('information');
});

Route::get('/manifesto', function () {
    return view('manifest');
});




Route::get('/fogo/{id}', 'FireController@get');
Route::get('/views/risk/{id}', 'FireController@getGeneralCard');
Route::get('/views/status/{id}', 'FireController@getStatusCard');