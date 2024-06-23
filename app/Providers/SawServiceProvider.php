<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SawService;

class SawServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the SawService
        $this->app->singleton(SawService::class, function ($app) {
            return new SawService();
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
