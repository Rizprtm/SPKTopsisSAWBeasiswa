<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TopsisService;

class TopsisServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the TopsisService
        $this->app->singleton(TopsisService::class, function ($app) {
            return new TopsisService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
