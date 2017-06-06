<?php

namespace App\Repositories;

use Sentinel;

class RoleRepository
{
    public static function store($aInputs)
    {
        $aInputs['slug'] = str_slug($aInputs['name']);
        
        $oRole = Sentinel::getRoleRepository()
            ->createModel()
            ->create($aInputs);
        
        $oRole->permissions = self::attachPermissions($aInputs);
        
        $oRole->save();
        
        self::attachUsers($oRole, $aInputs);
    }
    
    public static function update($id, $aInputs)
    {
        $aInputs['slug'] = str_slug($aInputs['name']);
        $oRole = Sentinel::findRoleById($id)
            ->fill($aInputs);
        
        $oRole->permissions = self::attachPermissions($aInputs);
        
        $oRole->save();
        
        self::attachUsers($oRole, $aInputs);
    }
    
    private static function attachPermissions($aInputs)
    {
        $aPermissions = [];
        
        if (isset($aInputs['permissions']) && $aInputs['permissions'] !== null)
        {
            foreach ($aInputs['permissions'] as $perm)
            {
                $aPermissions[$perm] = true;
            }
        }
        
        return $aPermissions;
    }
    
    private static function attachUsers($oRole, $aInputs)
    {
        if (isset($aInputs['users']) && $aInputs['users'] !== null) 
        {
            $aRoles = is_array($aInputs['users']) ? $aInputs['users'] : [$aInputs['users']];
            
            $oRole->users()->sync($aRoles);
        } 
        else 
        {
            $oRole->users()->detach();
        }
    }
}
