<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Office;
use App\Models\PersonalAccessToken;
use App\Observers\ClientObserver;
use App\Observers\OfficeObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
        Sanctum::ignoreMigrations();
        if ($this->app->environment('local') || $this->app->environment('staging') || $this->app->environment('acceptance') || $this->app->environment('production')) {
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
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Client::observe(ClientObserver::class);
        Office::observe(OfficeObserver::class);
    }
}
