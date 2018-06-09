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

Route::get('/', 'GenericController@getIndex')->name('home');
Route::get('/sobre', 'GenericController@getAbout')->name('about');
Route::get('/informacoes', 'GenericController@getInformation')->name('information');
Route::get('/notifications', 'GenericController@getNotifications')->name('notifications');
Route::get('/manifesto', 'GenericController@getManifest')->name('manifest');
Route::get('/partnerships', 'GenericController@getPartnerships')->name('partnerships');
Route::get('/change-language/{lang}', 'GenericController@getChangeLanguage')->name('changeLanguage');
Route::get('/fogo/{id}', 'FireController@get');
Route::get('/views/risk/{id}', 'FireController@getGeneralCard');
Route::get('/views/status/{id}', 'FireController@getStatusCard');
Route::get('/views/meteo/{id}', 'FireController@getMeteoCard');
