<?php

namespace App\Providers;

use App\Services\FirstMutualService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FirstMutualService::class, function ($app) {
            return new FirstMutualService();
        });
    }

    public function boot()
    {
        //
    }
}
