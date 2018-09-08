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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v2')->group(function () {
    // fires
    Route::get('/new/fires', 'ApiController@getFires');
    Route::get('/fires', 'ApiController@getFires');
    Route::get('/fires/{id}', 'ApiController@getFire');
    Route::get('/fires/status/{status}', 'ApiController@getFiresByStatus');
    Route::get('/fires/{fireId}/status', 'ApiController@getStatusByFire');
    Route::get('/fires/{fireId}/data', 'ApiController@getDataByFire');
    Route::get('/fires/{fireId}/danger', 'ApiController@getDangerByFire');

    // Fires Madeira
    Route::get('/madeira/fires', 'ApiController@dummyMethod');
    Route::get('/madeira/fires/{id}', 'ApiController@dummyMethod');
    Route::get('/madeira/fires/status', 'ApiController@dummyMethod');
    Route::get('/madeira/fires/status/{id}', 'ApiController@dummyMethod');
    Route::get('/madeira/fires/status/{status}', 'ApiController@dummyMethod');
    Route::get('/madeira/fires/data/{id}', 'ApiController@dummyMethod');
    Route::get('/madeira/fires/danger/{id}', 'ApiController@dummyMethod');



    // stats
    Route::get('/stats/week/', 'ApiController@dummyMethod');
    Route::get('/fires/8hours', 'ApiController@dummyMethod');
    Route::get('/fires/8hours/{day}', 'ApiController@dummyMethod');
    Route::get('/fires/last-night', 'ApiController@dummyMethod');

    // other
    Route::get('/now', 'ApiController@dummyMethod');
    Route::get('/warnings/{limit?}', 'ApiController@getWarnings');
});
