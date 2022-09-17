<?php

namespace App\Providers;

use App\Http\Controllers\Profile\WebauthnDestroyResponse;
use App\Http\Controllers\Profile\WebauthnUpdateResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use LaravelWebauthn\Facades\Webauthn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        RateLimiter::for('oauth2-socialite', function (Request $request) {
            return Limit::perMinute(5)->by(optional($request->user())->id ?: $request->ip());
        });

        Webauthn::updateViewResponseUsing(WebauthnUpdateResponse::class);
        Webauthn::destroyViewResponseUsing(WebauthnDestroyResponse::class);
    }
}
