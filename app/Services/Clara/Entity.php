<?php

namespace App\Services\Clara;

use File;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use App\Services\Clara\Generator\EntityGenerator;

class Entity
{
    private static $aDontTouchTables = [
        'activations', 
        'migrations', 
        'persistences', 
        'reminders', 
        'revisions',
        'roles', 
        'role_users', 
        'throttle', 
        'users'
    ];
    
    public static function getTables()
    {
	$oSchemaManager = self::getSchemaManager();
        $oTablesList    = $oSchemaManager->listTables();
        
        $aTablesName        = self::getTablesName($oTablesList);
        $aTablesRelations   = self::getTablesRelation($oTablesList, $oSchemaManager);
        
        return self::mergeRelationTables($aTablesName, $aTablesRelations);
    }
    
    private static function getSchemaManager()
    {
        $oConfig = new Configuration();
        $aConnectionParams = [
            'driver'    => 'pdo_mysql',
            'dbname'    => env('DB_DATABASE'),
            'user'      => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD')
        ];
        
        $oDatabaseConnection = DriverManager::getConnection($aConnectionParams, $oConfig);
        $oDatabaseConnection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
		
        return $oDatabaseConnection->getSchemaManager();
    }
    
    private static function getTablesName($oTablesList)
    {
        //Create an array with table name
        $aTablesName = [];
        foreach($oTablesList as $oTable)
        {
            if (!in_array($oTable->getName(), self::$aDontTouchTables))
            {
                $aTablesName[] = $oTable->getName();
            }
        }
        
        return $aTablesName;
    }
    
    private static function getTablesRelation($oTablesList, $oSchemaManager)
    {
        //Create an array with tables relations
        $aTablesRelations = [];
        foreach($oTablesList as $oTable)
        {
            $oRelations = $oSchemaManager->listTableForeignKeys($oTable->getName());
            foreach($oRelations as $oRelation)
            {
                $aTablesRelations[] = [
                    'table' => $oTable->getName(),
                    'column' => $oRelation->getLocalColumns()[0],
                    'related' => $oRelation->getForeignTableName()
                ];
            }
        }
        
        return $aTablesRelations;
    }
    
    private static function mergeRelationTables($aTablesName, $aTablesRelations)
    {
        $aTables = [];
        foreach($aTablesName as $aTable)
        {
            $aTables[$aTable] = self::addRelationToTable($aTable, $aTablesRelations);
        }
        
        return $aTables;
    }
    
    private static function addRelationToTable($aTable, $aTablesRelations)
    {
        $aRelations = [];
        foreach($aTablesRelations as $aRelation)
        {
            if($aRelation['related'] == $aTable)
            {                
                $aRelations[$aRelation['table']] = self::addRelatedTables($aTable, $aRelation, $aTablesRelations);
            }
        }
        
        return $aRelations;
    }
    
    private static function addRelatedTables($aTable, $aRelation, $aTablesRelations)
    {
        $aRelated = [];
        $aKeys = array_keys(array_column($aTablesRelations, 'table'), $aRelation['table']);

        foreach($aKeys as $iKey)
        {
            if($aTablesRelations[$iKey]['related'] != $aTable)
            {
                $aRelated[$aTablesRelations[$iKey]['related']] = $aTablesRelations[$iKey]['related']
                    .'-'.$aRelation['table']
                    .'-'.$aRelation['column']
                    .'-'.$aTablesRelations[$iKey]['column'];
            }
        }
        
        return $aRelated;
    }
    
    public static function store($aInputs)
    {
        $sRoutes    = "";
        $sRoutesApi = "";
        $sNavbar    = "";
        
        $sRoutesFile    = File::get(base_path().'/routes/web.php');
        $sRoutesApiFile = File::get(base_path().'/routes/api.php');
        $sNavbarFile    = File::get(base_path().'/app/Providers/AdminNavbarServiceProvider.php');
        
        foreach($aInputs['table'] as $sTable => $aFiles)
        {
            $sName      = ucfirst(camel_case($sTable));
            $sTitle     = ucwords(str_replace('_', ' ', $sTable));
            $sFolder    = str_replace('_', '-', $sTable);
            
            $aMany = self::setPivotRelationTable($sTable, $aInputs);
            
            EntityGenerator::generate($sName, $sTable, $sFolder, $aMany, $aFiles);
            
            $sRoutes    .= (strpos($sRoutesFile, '\''.$sName.'Controller\'') === false) 
                ? "Route::resource('".$sFolder."', '".$sName."Controller', ['names' => 'admin.". $sFolder ."']);\n\t" 
                : '';
            
            $sRoutesApi .= (strpos($sRoutesApiFile, '\''.$sName.'Controller@indexAjax\'') === false) 
                ? "Route::get('".$sFolder."/index/ajax', '".$sName."Controller@indexAjax')->name('admin.".$sFolder.".index.ajax');\n\t" 
                : '';
            
            $sRoutesApi .= (strpos($sRoutesApiFile, '\''.$sName.'Controller@selectAjax\'') === false) 
                ? "Route::get('".$sFolder."/select/ajax', '".$sName."Controller@selectAjax')->name('admin.".$sFolder.".select.ajax');\n\t" 
                : '';
                
            $sNavbar    .= (strpos($sNavbarFile, 'admin/'.$sFolder) === false) 
                ? "['title' => '".$sTitle."', 'link' => URL('admin/".$sFolder."')],\n\t\t" 
                : '';
        }
        
        $sRoutes    .= "//DummyControllers";
        $sRoutesApi .= "//DummyIndex";
        $sNavbar    .= "//DummyNavbar";
        
        //Add Controllers to routes
        $sRoutesFile = str_replace('//DummyControllers', $sRoutes, $sRoutesFile);
        File::put(base_path().'/routes/web.php', $sRoutesFile);
        
        //Add Controllers to API routes
        $sRoutesApiFile = str_replace('//DummyIndex', $sRoutesApi, $sRoutesApiFile);
        File::put(base_path().'/routes/api.php', $sRoutesApiFile);
        
        //Add Controllers to menu
        $sNavbarFile = str_replace('//DummyNavbar', $sNavbar, $sNavbarFile);
        File::put(base_path().'/app/Providers/AdminNavbarServiceProvider.php', $sNavbarFile);
    }
    
    private static function setPivotRelationTable($sTable, $aInputs)
    {
        $aMany = [];
        if(isset($aInputs['related-'.$sTable]))
        {
            foreach($aInputs['related-'.$sTable] as $sRelation)
            {
                if($sRelation != '0')
                {
                    $aRelation = explode('-', $sRelation);
                    $aMany[] = [
                        'related' => $aRelation[0],
                        'pivot' => $aRelation[1],
                        'foreign_key' => $aRelation[2],
                        'related_foreign_key' => $aRelation[3]
                    ];
                }
            }
        }
        
        return $aMany;
    }
}
