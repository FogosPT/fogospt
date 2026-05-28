<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GaiaController;
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

// Locale-agnostic endpoints — must live at the site root (the FCM service
// worker requires a root-scope URL, and the subscribe/unsubscribe AJAX is
// posted from any page regardless of locale).
Route::post('/notifications/subscribe', [GenericController::class, 'subscribe'])->name('notifications-subscribe');
Route::post('/notifications/unsubscribe', [GenericController::class, 'unsubscribe'])->name('notifications-unsubscribe');
Route::get('/firebase-messaging-sw.js', [GenericController::class, 'firebaseMessagingSw'])->name('firebase-messaging-sw');

// /lightnings sets its own Cache-Control + X-Cache headers in the
// controller (single-flight, stale-while-revalidate) — leave it alone.
Route::get('/lightnings', [FireController::class, 'getLightnings']);

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
Route::redirect('/gaia',            '/pt/gaia',            301);

Route::get('/fogo/{id}',         fn($id) => redirect("/pt/fogo/$id",         301));
Route::get('/fogo/{id}/detalhe', fn($id) => redirect("/pt/fogo/$id/detalhe", 301));
Route::get('/madeira/fogo/{id}', fn($id) => redirect("/pt/madeira/fogo/$id", 301));

// Cache profiles applied via Laravel's SetCacheHeaders middleware:
//   STATIC    — info pages that rarely change. CDN holds for 1h, browser 5 min.
//   MAP       — home/Madeira map. No server data inline, but data injected by
//               JS is highly dynamic, so keep CDN window short.
//   FIRES     — pages that render server-fetched fire/warning/stats data.
//               Short CDN window absorbs traffic spikes on hot fires without
//               making the data feel stale.
//
// `no_cache=0;private=0` strips the no-cache/private directives Symfony
// pre-seeds into every Response — without that, the Cache-Control header
// ships as e.g. `max-age=300, no-cache, public, s-maxage=3600`, which
// forces CDNs and browsers to revalidate every hit and defeats s-maxage.
$CACHE_STATIC = 'cache.headers:public;max_age=300;s_maxage=3600;no_cache=0;private=0';
$CACHE_MAP    = 'cache.headers:public;max_age=60;s_maxage=120;no_cache=0;private=0';
$CACHE_FIRES  = 'cache.headers:public;max_age=30;s_maxage=60;no_cache=0;private=0';

Route::prefix('{locale}')->middleware('locale.match')->group(function () use ($CACHE_STATIC, $CACHE_MAP, $CACHE_FIRES) {
    Route::get('/', [GenericController::class, 'getIndex'])->name('home')->middleware($CACHE_MAP);
    Route::get('/gaia', [GaiaController::class, 'getIndex'])->name('gaia')->middleware($CACHE_MAP);
    Route::get('/outros', [GenericController::class, 'getOtherFires'])->name('other-fires')->middleware($CACHE_FIRES);
    Route::get('/madeira', [GenericController::class, 'getIndexMadeira'])->name('homeMadeira')->middleware($CACHE_MAP);
    Route::get('/sobre', [GenericController::class, 'getAbout'])->name('about')->middleware($CACHE_STATIC);
    Route::get('/lista', [GenericController::class, 'getTable'])->name('list')->middleware($CACHE_FIRES);
    Route::get('/tabela', [GenericController::class, 'getTable'])->name('table')->middleware($CACHE_FIRES);
    Route::get('/avisos', [GenericController::class, 'getWarnings'])->name('warnings')->middleware($CACHE_FIRES);
    Route::get('/madeira/avisos', [GenericController::class, 'getWarningsMadeira'])->name('warningsMadeira')->middleware($CACHE_FIRES);
    Route::get('/informacoes', [GenericController::class, 'getInformation'])->name('information')->middleware($CACHE_STATIC);
    Route::get('/parceiros', [GenericController::class, 'getPartnerships'])->name('partnerships')->middleware($CACHE_STATIC);
    Route::get('/estatisticas', [GenericController::class, 'getStats'])->name('stats')->middleware($CACHE_FIRES);
    Route::get('/api', [GenericController::class, 'api'])->name('api')->middleware($CACHE_STATIC);
    Route::get('/api-termos', [GenericController::class, 'apiTerms'])->name('api-termos')->middleware($CACHE_STATIC);

    Route::get('/fogo/{id}', [FireController::class, 'get'])->name('fire')->middleware($CACHE_FIRES);
    Route::get('/fogo/{id}/detalhe', [FireController::class, 'getDetails'])->name('fireDetail')->middleware($CACHE_FIRES);
    Route::get('/madeira/fogo/{id}', [FireController::class, 'getMadeira'])->middleware($CACHE_FIRES);
    Route::get('/new/fires', [FireController::class, 'getAll']);

    Route::get('/change-language/{lang}', [GenericController::class, 'getChangeLanguage'])->name('changeLanguage');

    Route::get('/notificacoes', [GenericController::class, 'getNotifications'])->name('notifications')->middleware($CACHE_STATIC);
    Route::get('/privacy-policy', [GenericController::class, 'getPrivacyPolicy'])->name('privacy-policy')->middleware($CACHE_STATIC);

    if (app()->environment() !== 'production') {
        Route::get('/manifesto', [GenericController::class, 'getManifest'])->name('manifest');
    }
});

Route::middleware(['locale.match', $CACHE_FIRES])->group(function () {
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
    Route::get('/ipma-wind', [ApiController::class, 'getIpmaWind'])->name('getIpmaWind');
    Route::get('/ipma-reference-time', [ApiController::class, 'getIpmaReferenceTime'])->name('getIpmaReferenceTime');
    Route::get('/ipma-point/{lat}/{lng}', [ApiController::class, 'getIpmaPoint'])
        ->where(['lat' => '-?\d+(\.\d+)?', 'lng' => '-?\d+(\.\d+)?'])
        ->name('getIpmaPoint');
    Route::get('/ipma-wms', [ApiController::class, 'getIpmaWms'])->name('getIpmaWms');
    Route::get('/ipma-value', [ApiController::class, 'getIpmaValue'])->name('getIpmaValue');
});

Route::prefix('gaia/v1')->group(function () {
    Route::get('/events',                   [GaiaController::class, 'events']);
    Route::get('/events/{id}',              [GaiaController::class, 'eventDetail'])->where('id', '\d+');
    Route::get('/events/{id}/timeline',     [GaiaController::class, 'timeline'])->where('id', '\d+');
    Route::get('/events/{id}/acquisitions', [GaiaController::class, 'acquisitions'])->where('id', '\d+');
    Route::get('/detections',               [GaiaController::class, 'detections']);
    Route::get('/delineations',             [GaiaController::class, 'delineations']);
});
