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
        $this->publishAdminLte();
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
    
    protected function publishAdminLte()
    {
        $this->publishes([
            'vendor/almasaeed2010/adminlte/dist'				=> public_path('adminlte'),
            'vendor/almasaeed2010/adminlte/bower_components'	=> public_path('bower_components'),
            'vendor/almasaeed2010/adminlte/plugins'				=> public_path('adminlte/plugins')
        ], 'public');
    }
}
