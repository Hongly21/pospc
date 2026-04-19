<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }


    // public function boot(): void
    // {
    //     Paginator::useBootstrapFive();
    // }
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Force HTTPS in production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
