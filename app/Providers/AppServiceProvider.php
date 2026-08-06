<?php

namespace App\Providers;

use App\Services\OmdbService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OmdbService::class, function () {
            return new OmdbService(new Client([
                'base_uri' => config('services.omdb.base_url'),
                'timeout' => 8,
                'http_errors' => false,
            ]));
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
