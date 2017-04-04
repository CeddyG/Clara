<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class Permission
{
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
                if($sCurrentPerm != $sTmpName[0])
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
}
