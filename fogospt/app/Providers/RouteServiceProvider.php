<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Fire ids are forwarded to source.fogos.pt — reject anything that
        // isn't a plain identifier before it reaches a controller.
        Route::pattern('id', '[0-9A-Za-z_-]+');

        $this->routes(function () {
            Route::middleware('web')
                 ->group(base_path('routes/web.php'));
        });
    }
}
