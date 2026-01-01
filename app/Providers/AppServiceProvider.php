<?php

namespace App\Providers;

use App\Socialite\BungieProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Socialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Socialite::extend('bungie', function ($app) {
            $config = $app['config']['services.bungie'];
            return Socialite::buildProvider(BungieProvider::class, $config);
        });
    }
}
