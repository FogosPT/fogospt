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
Route::get('/tabela', 'GenericController@getTable')->name('table');
Route::get('/avisos', 'GenericController@getWarnings')->name('warnings');
Route::get('/madeira/avisos', 'GenericController@getWarningsMadeira')->name('warningsMadeira');
Route::get('/informacoes', 'GenericController@getInformation')->name('information');
Route::get('/parceiros', 'GenericController@getPartnerships')->name('partnerships');
Route::get('/estatisticas', 'GenericController@getStats')->name('stats');


Route::get('/fogo/{id}', 'FireController@get')->name('fire');
Route::get('/fogo/{id}/detalhe', 'FireController@getDetails')->name('fireDetail');
Route::get('/madeira/fogo/{id}', 'FireController@getMadeira');
Route::get('/new/fires', 'FireController@getAll');


Route::get('/change-language/{lang}', 'GenericController@getChangeLanguage')->name('changeLanguage');


Route::get('/views/risk/{id}', 'FireController@getGeneralCard');
Route::get('/views/status/{id}', 'FireController@getStatusCard');
Route::get('/views/meteo/{id}', 'FireController@getMeteoCard');
Route::get('/views/extra/{id}', 'FireController@getExtraCard');
Route::get('/views/twitter/{id}', 'FireController@getTwitterCard');
Route::get('/views/shares/{id}', 'FireController@getSharesCard');


Route::get('/madeira/views/risk/{id}', 'FireController@getGeneralCardMadeira');
Route::get('/madeira/views/status/{id}', 'FireController@getStatusCardMadeira');
Route::get('/madeira/views/meteo/{id}', 'FireController@getMeteoCardMadeira');
Route::get('/madeira/views/extra/{id}', 'FireController@getExtraCardMadeira');

Route::get('/lightnings', 'FireController@getLightnings');


Route::get('/notificacoes', 'GenericController@getNotifications')->name('notifications');
Route::post('/notifications/subscribe', 'GenericController@subscribe')->name('notifications-subscribe');
Route::post('/notifications/unsubscribe', 'GenericController@unsubscribe')->name('notifications-subscribe');

Route::group(['prefix' => 'v1'], function () {
    Route::get('/mobile-contributors', 'ApiController@getMobileContributors')->name('getMobileContributors');
    Route::get('/modis', 'ApiController@getModis')->name('getModis');
    Route::get('/viirs', 'ApiController@getVIIRS')->name('getVIIRS');
});

if (app()->environment() !== 'production') {
    Route::get('/manifesto', 'GenericController@getManifest')->name('manifest');
}
