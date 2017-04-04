<?php

namespace App\Providers;

use View;
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
            $aNavbar = [
                ['title' => 'Accueil', 'link' => url('/admin')],
                ['title' => 'Admin', 'link' => URL('admin/admin')],
		['title' => 'Auteur', 'link' => URL('admin/auteur')],
		['title' => 'Categorie Livre', 'link' => URL('admin/categorie-livre')],
		['title' => 'Categorie Rayon', 'link' => URL('admin/categorie-rayon')],
		['title' => 'Livre', 'link' => URL('admin/livre')],
		['title' => 'Poste', 'link' => URL('admin/poste')],
		['title' => 'Rayon', 'link' => URL('admin/rayon')],
		//DummyNavbar
            ];
            
            $aNavbarParam = [
                [
                    'Users',
                    [
                        ['title' => 'Liste', 'link' => URL('admin/user')],
                        ['title' => 'Groupes', 'link' => URL('admin/group')]
                    ]
                ],
                ['title' => 'Entity', 'link' => URL('admin/entity')]
            ];
            
            $sNavbar = Navigation::pills($aNavbar, ['class' => 'sidebar-menu'])->stacked();
            $sNavbarParam = Navigation::pills($aNavbarParam, ['class' => 'sidebar-menu'])->stacked();
            
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
