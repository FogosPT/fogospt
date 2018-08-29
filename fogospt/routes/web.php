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
Route::get('/madeira', 'GenericController@getIndexMadeira')->name('homeMadeira');
Route::get('/sobre', 'GenericController@getAbout')->name('about');
Route::get('/lista', 'GenericController@getList')->name('list');
Route::get('/avisos', 'GenericController@getWarnings')->name('warnings');
Route::get('/informacoes', 'GenericController@getInformation')->name('information');
Route::get('/parceiros', 'GenericController@getPartnerships')->name('partnerships');
Route::get('/estatisticas', 'GenericController@getStats')->name('stats');


Route::get('/fogo/{id}', 'FireController@get');
Route::get('/new/fires', 'FireController@getAll');


Route::get('/change-language/{lang}', 'GenericController@getChangeLanguage')->name('changeLanguage');


Route::get('/views/risk/{id}', 'FireController@getGeneralCard');
Route::get('/views/status/{id}', 'FireController@getStatusCard');
Route::get('/views/meteo/{id}', 'FireController@getMeteoCard');
Route::get('/views/extra/{id}', 'FireController@getExtraCard');

Route::get('/notificacoes', 'GenericController@getNotifications')->name('notifications');
Route::post('/notifications/subscribe', 'GenericController@subscribe')->name('notifications-subscribe');
Route::post('/notifications/unsubscribe', 'GenericController@unsubscribe')->name('notifications-subscribe');

if(ENV('APP_ENV') !== 'production'){
    Route::get('/manifesto', 'GenericController@getManifest')->name('manifest');
}