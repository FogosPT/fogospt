<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GenericController;

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

Route::redirect('/', '/pt');

// Backward compatibility — redirect legacy links (without locale) to /pt/
Route::redirect('/outros',          '/pt/outros',          301);
Route::redirect('/madeira',         '/pt/madeira',         301);
Route::redirect('/sobre',           '/pt/sobre',           301);
Route::redirect('/lista',           '/pt/lista',           301);
Route::redirect('/tabela',          '/pt/tabela',          301);
Route::redirect('/avisos',          '/pt/avisos',          301);
Route::redirect('/madeira/avisos',  '/pt/madeira/avisos',  301);
Route::redirect('/informacoes',     '/pt/informacoes',     301);
Route::redirect('/parceiros',       '/pt/parceiros',       301);
Route::redirect('/estatisticas',    '/pt/estatisticas',    301);
Route::redirect('/api',             '/pt/api',             301);
Route::redirect('/api-termos',      '/pt/api-termos',      301);
Route::redirect('/notificacoes',    '/pt/notificacoes',    301);
Route::redirect('/privacy-policy',  '/pt/privacy-policy',  301);

Route::get('/fogo/{id}',         fn($id) => redirect("/pt/fogo/$id",         301));
Route::get('/fogo/{id}/detalhe', fn($id) => redirect("/pt/fogo/$id/detalhe", 301));
Route::get('/madeira/fogo/{id}', fn($id) => redirect("/pt/madeira/fogo/$id", 301));

Route::prefix('{locale}')->middleware('locale.match')->group(function () {
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
    Route::get('/api', [GenericController::class, 'api'])->name('api');
    Route::get('/api-termos', [GenericController::class, 'apiTerms'])->name('api-termos');

    Route::get('/fogo/{id}', [FireController::class, 'get'])->name('fire');
    Route::get('/fogo/{id}/detalhe', [FireController::class, 'getDetails'])->name('fireDetail');
    Route::get('/madeira/fogo/{id}', [FireController::class, 'getMadeira']);
    Route::get('/new/fires', [FireController::class, 'getAll']);

    Route::get('/change-language/{lang}', [GenericController::class, 'getChangeLanguage'])->name('changeLanguage');

    Route::get('/lightnings', [FireController::class, 'getLightnings']);

    Route::get('/notificacoes', [GenericController::class, 'getNotifications'])->name('notifications');
    Route::get('/privacy-policy', [GenericController::class, 'getPrivacyPolicy'])->name('privacy-policy');
    Route::post('/notifications/subscribe', [GenericController::class, 'subscribe'])->name('notifications-subscribe');
    Route::post('/notifications/unsubscribe', [GenericController::class, 'unsubscribe'])->name('notifications-unsubscribe');

    if (app()->environment() !== 'production') {
        Route::get('/manifesto', [GenericController::class, 'getManifest'])->name('manifest');
    }
});

Route::middleware('locale.match')->group(function () {
    Route::get('/{locale}/views/risk/{id}', [FireController::class, 'getGeneralCard']);
    Route::get('/{locale}/views/status/{id}', [FireController::class, 'getStatusCard']);
    Route::get('/{locale}/views/meteo/{id}', [FireController::class, 'getMeteoCard']);
    Route::get('/{locale}/views/extra/{id}', [FireController::class, 'getExtraCard']);
    Route::get('/{locale}/views/shares/{id}', [FireController::class, 'getSharesCard']);

    Route::get('/{locale}/madeira/views/risk/{id}', [FireController::class, 'getGeneralCardMadeira']);
    Route::get('/{locale}/madeira/views/status/{id}', [FireController::class, 'getStatusCardMadeira']);
    Route::get('/{locale}/madeira/views/meteo/{id}', [FireController::class, 'getMeteoCardMadeira']);
    Route::get('/{locale}/madeira/views/extra/{id}', [FireController::class, 'getExtraCardMadeira']);
});

Route::group(['prefix' => 'v1'], function () {
    Route::get('/mobile-contributors', [ApiController::class, 'getMobileContributors'])->name('getMobileContributors');
    Route::get('/modis', [ApiController::class, 'getModis'])->name('getModis');
    Route::get('/viirs', [ApiController::class, 'getVIIRS'])->name('getVIIRS');
});
