<?php

namespace App\Providers;

use View;
use Route;
use Sentinel;
use Navigation;

use Illuminate\Support\ServiceProvider;

class AdminNavbarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('admin.sidebar', function($view)
        {
            $aConfigNavbar = config('clara.navbar');
            
            $aNavbar = [];
            foreach ($aConfigNavbar as $sKey => $sTitle)
            {
                if (Sentinel::hasAccess('admin.'.$sKey.'.index') && Route::has('admin.'.$sKey.'.index'))
                {
                    $aNavbar[] = ['title' => $sTitle, 'link' => route('admin.'.$sKey.'.index')];
                }
            }
            
            $aNavbarParam = [
                [
                    'Users',
                    [
                        ['title' => 'Liste', 'link' => URL('admin/user')],
                        ['title' => 'Groupes', 'link' => URL('admin/group')]
                    ]
                ],
                ['title' => 'Dataflow', 'link' => URL('admin/dataflow')],
                ['title' => 'Entity', 'link' => URL('admin/clara-entity')]
            ];
            
            $sNavbar        = Navigation::pills($aNavbar, ['class' => 'sidebar-menu'])->stacked();
            $sNavbarParam   = Navigation::pills($aNavbarParam, ['class' => 'sidebar-menu'])->stacked();
            
            $view->with('navbar', $sNavbar);
            $view->with('navbarparam', $sNavbarParam);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
