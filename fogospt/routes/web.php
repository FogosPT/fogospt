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

Route::get('/', 'GenericController@getIndex');
Route::get('/sobre', 'GenericController@getAbout');
Route::get('/informacoes', 'GenericController@getInformation');
Route::get('/manifesto', 'GenericController@getManifest');
Route::get('/fogo/{id}', 'FireController@get');
Route::get('/views/risk/{id}', 'FireController@getGeneralCard');
Route::get('/views/status/{id}', 'FireController@getStatusCard');