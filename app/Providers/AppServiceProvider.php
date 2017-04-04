<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            'vendor/almasaeed2010/adminlte/dist'        => public_path('adminlte'),
            'vendor/almasaeed2010/adminlte/bootstrap'   => public_path('bootstrap'),
            'vendor/almasaeed2010/adminlte/plugins'     => public_path('adminlte/plugins')
        ], 'public');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
