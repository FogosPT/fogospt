<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GenericController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [GenericController::class, 'getIndex'])->name('home');
Route::get('/outros', [GenericController::class, 'getOtherFires'])->name('other-fires');
Route::get('/madeira', [GenericController::class, 'getIndexMadeira'])->name('homeMadeira');
Route::get('/sobre', [GenericController::class, 'getAbout'])->name('about');
Route::get('/lista', [GenericController::class, 'getTable'])->name('list');
Route::get('/tabela', [GenericController::class, 'getTable'])->name('table');
Route::get('/avisos', [GenericController::class, 'getWarnings'])->name('warnings');
Route::get('/madeira/avisos', [GenericController::class, 'getWarningsMadeira'])->name('warningsMadeira');
Route::get('/informacoes', [GenericController::class, 'getInformation'])->name('information');
Route::get('/parceiros', [GenericController::class, 'getPartnerships'])->name('partnerships');
Route::get('/estatisticas', [GenericController::class, 'getStats'])->name('stats');

Route::get('/fogo/{id}', [FireController::class, 'get'])->name('fire');
Route::get('/fogo/{id}/detalhe', [FireController::class, 'getDetails'])->name('fireDetail');
Route::get('/madeira/fogo/{id}', [FireController::class, 'getMadeira']);
Route::get('/new/fires', [FireController::class, 'getAll']);

Route::get('/change-language/{lang}', [GenericController::class, 'getChangeLanguage'])->name('changeLanguage');

Route::get('/views/risk/{id}', [FireController::class, 'getGeneralCard']);
Route::get('/views/status/{id}', [FireController::class, 'getStatusCard']);
Route::get('/views/meteo/{id}', [FireController::class, 'getMeteoCard']);
Route::get('/views/extra/{id}', [FireController::class, 'getExtraCard']);
Route::get('/views/twitter/{id}', [FireController::class, 'getTwitterCard']);
Route::get('/views/shares/{id}', [FireController::class, 'getSharesCard']);

Route::get('/madeira/views/risk/{id}', [FireController::class, 'getGeneralCardMadeira']);
Route::get('/madeira/views/status/{id}', [FireController::class, 'getStatusCardMadeira']);
Route::get('/madeira/views/meteo/{id}', [FireController::class, 'getMeteoCardMadeira']);
Route::get('/madeira/views/extra/{id}', [FireController::class, 'getExtraCardMadeira']);

Route::get('/lightnings', [FireController::class, 'getLightnings']);

Route::get('/notificacoes', [GenericController::class, 'getNotifications'])->name('notifications');
Route::post('/notifications/subscribe', [GenericController::class, 'subscribe'])->name('notifications-subscribe');
Route::post('/notifications/unsubscribe', [GenericController::class, 'unsubscribe'])->name('notifications-unsubscribe');

Route::prefix('v1')->group(function () {
    Route::get('/mobile-contributors', [ApiController::class, 'getMobileContributors'])->name('getMobileContributors');
    Route::get('/modis', [ApiController::class, 'getModis'])->name('getModis');
    Route::get('/viirs', [ApiController::class, 'getVIIRS'])->name('getVIIRS');
});

if (app()->environment() !== 'production') {
    Route::get('/manifesto', [GenericController::class, 'getManifest'])->name('manifest');
}
