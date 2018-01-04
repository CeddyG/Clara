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
        $this->publishEntityGenerator();
        $this->publishInstaller();
        $this->publishDataflow();
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
    
    protected function publishEntityGenerator()
    {
        $this->publishes([
            'vendor/ceddyg/clara-entity-generator/src/resources' => base_path('resources')
        ], 'generator-stubs');

        $this->publishes([
            'vendor/ceddyg/clara-entity-generator/src/app' => base_path('app')
        ], 'generator-services');
    }
    
    protected function publishInstaller()
    {
        $this->publishes([
            'vendor/ceddyg/clara-installer/src/app' => base_path('app')
        ], 'installer-services');

        $this->publishes([
            'vendor/ceddyg/clara-installer/src/database' => base_path('database')
        ], 'installer-migrations');

        $this->publishes([
            'vendor/ceddyg/clara-installer/src/resources' => base_path('resources')
        ], 'installer-views'); 
    }
    
    protected function publishDataflow()
    {
        $this->publishes([
            'vendor/ceddyg/clara-dataflow/src/app' => base_path('app')
        ], 'dataflow-services');

        $this->publishes([
            'vendor/ceddyg/clara-dataflow/src/database' => base_path('database')
        ], 'dataflow-migrations');

        $this->publishes([
            'vendor/ceddyg/clara-dataflow/src/resources' => base_path('resources')
        ], 'dataflow-views'); 
    }
}
