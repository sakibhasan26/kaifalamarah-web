<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
ini_set('memory_limit','-1');

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // config('flutterwave')['publicKey'] = 'FLWPUBK_TEST-8c91f68d3221f80efdd1d7f9fa9fb2d4-X';
        // config('flutterwave')['secretKey'] = 'FLWSECK_TEST-54bf1bed8aaf1687d2df33dcd74ebbe7-X';

        Paginator::useBootstrapFive();
        Schema::defaultStringLength(191);
    }
}
