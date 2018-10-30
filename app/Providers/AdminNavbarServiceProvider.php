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
            $sRoute     = Route::getCurrentRoute()->getName();
            $aAction    = explode('.', $sRoute);
            $sEntity    = isset($aAction[1]) ? $aAction[1] : '';
            
            $aConfigNavbar = config('clara.navbar');
            $aNavbar = [];
            
            foreach ($aConfigNavbar as $sKey => $mTitle)
            {
                if (Sentinel::hasAccess('admin.'.$sKey.'.index') && Route::has('admin.'.$sKey.'.index'))
                {
                    $aNavbar[] = [
                        'title' => strpos($mTitle, '.') !== false ? __($mTitle) : $mTitle, 
                        'link'  => route('admin.'.$sKey.'.index'), 'active' => $sEntity == $sKey
                    ];
                }
                
                if (is_array($mTitle))
                {
                    $aSubNav = [];
                    
                    foreach ($mTitle[1] as $sSubKey => $sSubTitle)
                    {
                        $aSubNav[] = [
                            'title' => strpos($sSubTitle, '.') !== false ? __($sSubTitle) : $sSubTitle,
                            'link' => route('admin.'.$sSubKey.'.index'), 'active' => $sEntity == $sSubKey
                        ];
                    }
                    
                    $sMainTitle = strpos($mTitle[0], '.') !== false ? __($mTitle[0]) : $mTitle[0];
                    
                    $aNavbar[] = [
                        $sMainTitle, 
                        $aSubNav
                    ];
                }
            }
            
            $aNavbarParam = [];
            
            if (Sentinel::hasAccess('admin.user.index') && Route::has('admin.user.index'))
            {
                if (Sentinel::hasAccess('admin.group.index') && Route::has('admin.group.index'))
                {
                    $aNavbarParam[] = [
                        'Utilisateurs',
                        [
                            ['title' => 'Liste', 'link' => URL('admin/user'), 'active' => $sEntity == 'user'],
                            ['title' => 'Groupes', 'link' => URL('admin/group'), 'active' => $sEntity == 'group']
                        ]
                    ];
                }
                else
                {
                    $aNavbarParam[] = ['title' => 'Utilisateurs', 'link' => URL('admin/user'), 'active' => $sEntity == 'user']                    ;
                }
            }
            
            if (Sentinel::hasAccess('admin.dataflow.index') && Route::has('admin.dataflow.index'))
            {
                $aNavbarParam[] = ['title' => 'Dataflow', 'link' => URL('admin/dataflow'), 'active' => $sEntity == 'dataflow'];
            }
            
            if (Sentinel::hasAccess('clara-entity.index') && Route::has('clara-entity.index'))
            {
                $aNavbarParam[] = ['title' => 'Entity', 'link' => URL('admin/clara-entity'), 'active' => $sEntity == 'clara-entity'];
            }
            
            $sNavbar        = Navigation::pills($aNavbar, ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'])->stacked();
            $sNavbarParam   = Navigation::pills($aNavbarParam, ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'])->stacked();
            
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
