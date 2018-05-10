<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use View;
use Route;

class PermissionServiceProvide extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('admin.user.form', function($view)
        {            
            $view->with('aPermissions', self::getPermissions());
        });
        
        View::composer('admin.group.form', function($view)
        {            
            $view->with('aPermissions', self::getPermissions());
        });
    }
    
    public static function getPermissions()
    {
        $oPerms = Route::getRoutes();
        
        //Liste de permissions possibles à partir des routes existantes
        $aPermissions = [];
        $sCurrentPerm = '';
        
        foreach($oPerms as $oPerm)
        {
            if($oPerm->getName() != ''
            && $oPerm->getName() != 'authenticate'
            && strrpos($oPerm->getName(), 'sentinel') === false
            && strrpos($oPerm->getName(), 'group') === false
            && strrpos($oPerm->getName(), 'debugbar') === false)
            {
                $sName = preg_replace('/admin./', '', $oPerm->getName(), 1);
                
                $sTmpName = explode('.', $sName);
                if($sCurrentPerm != $sTmpName[0] && $sTmpName[0] != 'admin')
                {
                    $aPermissions['admin.'.$sTmpName[0].'.*'] = $sTmpName[0];
                    $sCurrentPerm = $sTmpName[0];
                }
                
                $sName = str_replace('.', ' ', $sName);
                $aPermissions[$oPerm->getName()] = $sName;
            }
            
        }
        
        asort($aPermissions);
        $aPermissions = ['*' => 'all'] + $aPermissions;
        
        return $aPermissions;
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
