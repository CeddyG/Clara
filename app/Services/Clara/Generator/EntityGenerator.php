<?php

namespace App\Services\Clara\Generator;

use Facades\App\Services\Clara\Generator\FormGenerator;
use Facades\App\Services\Clara\Generator\IndexGenerator;
use Facades\App\Services\Clara\Generator\ModelGenerator;
use Facades\App\Services\Clara\Generator\RequestGenerator;
use Facades\App\Services\Clara\Generator\ControllerGenerator;
use Facades\App\Services\Clara\Generator\RepositoryGenerator;
use Facades\App\Services\Clara\Generator\TraductionGenerator;

class EntityGenerator
{    
    public static function generate($sName, $sTable, $sFolder, $aMany, $aFiles)
    {
        //Table name
        $sTable = strtolower($sTable);
        
        //We get the table columns
        $aColumns = self::getColumns($sTable);
        
        //We get the table relations
        $aRelations = self::getRelations($sTable, $aMany);
        
        if (isset($aFiles['form']))
            FormGenerator::generate($sTable, $sFolder, $aColumns, $aRelations);
        
        if (isset($aFiles['index']))
            IndexGenerator::generate($sFolder, $aColumns);
        
        if (isset($aFiles['model']))
            ModelGenerator::generate($sName, $sTable, $aColumns, $aRelations);
        
        if (isset($aFiles['request']))
            RequestGenerator::generate($sName, $aColumns);
        
        if (isset($aFiles['controller']))
            ControllerGenerator::generate($sName, $sFolder);
        
        if (isset($aFiles['repository']))
            RepositoryGenerator::generate($sName, $sTable, $aColumns, $aRelations);
        
        if (isset($aFiles['traduction']))
            TraductionGenerator::generate($sTable, $sFolder, $aColumns);
    }
    
    private static function getTablesDetails($sTableName)
    {
        // single column thru Laravel API
        $oConnection = \DB::connection();
        $oConnection->getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');
        
        $oConnection->getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('tinyint', 'smallint');
        
        //get the underlying Doctrine manager
        $oShemaManager = $oConnection->getDoctrineSchemaManager();
        
        //\Doctrine\DBAL\Schema\Table
        return $oShemaManager->listTableDetails($sTableName); 
    }
    
    private static function getColumns($sTableName)
    {
        $oTable = self::getTablesDetails($sTableName);
        
        //array of \Doctrine\DBAL\Schema\Column
        $aColumns = $oTable->getColumns(); 
        $aForeignKey = $oTable->getForeignKeys();
        
        $aFk = [];
        foreach($aForeignKey as $oFk)
        {
            $aFk[] = [
                "column"    => $oFk->getColumns()[0],
                "table"     => $oFk->getForeignTableName()
            ];
        }
        
        return self::formatteColumns($aColumns, $aFk, $oTable);
    }
    
    private static function formatteColumns(array $aColumns, $aFk, $oTable)
    {
        $aFormattedColumn = [];
        
        foreach ($aColumns as $oColumn)
        {
            $aColumn = [];
            
            $aColumn['field']    = $oColumn->getName();
            $aColumn['type']     = $oColumn->getType()->getName();
            $aColumn['length']   = $oColumn->getLength();
            
            self::checkForKey($aColumn, $oTable, $oColumn, $aFk);
            
            $aFormattedColumn[] = $aColumn;
        }
        
        return $aFormattedColumn;
    }
    
    private static function checkForKey(&$aColumn, $oTable, $oColumn, $aFk)
    {
        $aColumn['exclude'] = false;
        
        if($oTable->getPrimaryKey()->getColumns()[0] == $oColumn->getName())
        {
            $aColumn['key']     = 'PRI';
            $aColumn['exclude'] = true;
        }
        else if(in_array($oColumn->getName(), array_column($aFk, 'column')))
        {
            $iKey       = array_search($oColumn->getName(), array_column($aFk, 'column'));
            $sTableFk   = $aFk[$iKey]["table"];
            $aFields    = self::getPrimaryAndFirstField($sTableFk);
            
            $aColumn['tableFk']     = $sTableFk;
            $aColumn['key']         = 'FK';
            $aColumn['name_field']  = $aFields['first_field'];
        }
        else
        {
            $aColumn['key'] = '';
        }
    }

    private static function getRelations($sTableName, $aMany)
    {
        // single column thru Laravel API
        $oConnection    = \DB::connection();
        $sDatabase      = $oConnection->getDatabaseName();
        $oRelations     = \DB::select(
            "select TABLE_NAME,COLUMN_NAME "
            . "from INFORMATION_SCHEMA.KEY_COLUMN_USAGE "
            . "where REFERENCED_TABLE_NAME = '". $sTableName ."' "
            . "AND REFERENCED_TABLE_SCHEMA = '". $sDatabase ."'"
        );
        
        return self::BuildRelations($oRelations, $aMany);
    }
    
    private static function BuildRelations($oRelations, $aMany)
    {
        $aRelations = [];
        
        foreach($oRelations as $oRelation)
        {
            $sRelatedTab = $oRelation->TABLE_NAME;
            $sRelatedKey = $oRelation->COLUMN_NAME;
            
            $iKey = array_search($sRelatedTab, array_column($aMany, 'pivot'));
            
            if($iKey !== false)
            {
                $aRelations[] = self::buildRelationsPivot($sRelatedTab, $sRelatedKey, $aMany, $iKey);
            }
            else
            {
                $aRelations[] = self::buildRelationsNoPivot($sRelatedTab, $sRelatedKey);
            }
        }
        
        return $aRelations;
    }
    
    private static function buildRelationsPivot($sRelatedTab, $sRelatedKey, $aMany, $iKey)
    {
        $aFields = self::getPrimaryAndFirstField($aMany[$iKey]['related']);
                
        return [
            'related'       => $aMany[$iKey]['related'],
            'fk'            => $sRelatedKey,
            'pivot'         => $sRelatedTab,
            'fk_related'    => $aMany[$iKey]['related_foreign_key'],
            'id_related'    => $aFields['id'],
            'name_field'    => $aFields['first_field']
        ];
    }
    
    private static function buildRelationsNoPivot($sRelatedTab, $sRelatedKey)
    {
        $aFields = self::getPrimaryAndFirstField($sRelatedTab);
                
        return [
            'related'       => $sRelatedTab,
            'fk'            => $sRelatedKey,
            'id_related'    => $aFields['id'],
            'name_field'    => $aFields['first_field']
        ];
    }
    
    private static function getPrimaryAndFirstField($sRelatedTab)
    {
        $aRelatedColumns = self::getColumns($sRelatedTab);
                
        $sId        = 'id';
        $sName      = 'id';
        $bIdOk      = false;
        $bNameOk    = false;
        foreach($aRelatedColumns as $aColumn)
        {
            if($aColumn['key'] == 'PRI')
            {
                $sId = $aColumn['field'];
                $bIdOk = true;
            }
            
            if($aColumn['key'] == '')
            {
                $sName = $aColumn['field'];
                $bNameOk = true;
            }
            
            if ($bIdOk && $bNameOk)
            {
                break;
            }
        }
        
        return [
            'id'            => $sId,
            'first_field'   => $sName
        ];
    }
}
