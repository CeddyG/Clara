<?php

namespace App\Services\Clara\Generator;

use File;

class ModelGenerator extends BaseGenerator
{
    /*
     * Path where to generate the file.
     * 
     * @var string
     */
    static $PATH = '/app/Models/';
    
    /*
     * Stub's name to use to create the file.
     * 
     * @var string
     */
    static $STUB = 'Model';
    
    /**
     * Directory for specific stubs.
     * 
     * @var string
     */
    static $STUB_DIR = '/resources/stubs/model/';

    /**
     * Column to exclude.
     * 
     * @var array 
     */
    protected $aExclude = ['id', 'password', 'created_at', 'updated_at'];

    /**
     * Store stubs in it, to not load stubs from file a second time.
     * 
     * @var array
     */
    protected $aStubs = [];

    /**
     * Generate the file.
     * 
     * @return void
     */
    public function generate($sName, $sTable, $aColumns, $aRelations)
    {
        $sId = 'id';
        
        $aField = $this->buildFields($sId, $aColumns);
                
        self::createFile($sName.'.php', [
            'Class'     => $sName,
            'Table'     => $sTable,
            'Fillable'  => $aField['fillable'],
            'Id'        => $sId,
            'Date'      => $aField['date'],
            'Carbon'    => $aField['date'] != '' ? 'use Carbon\Carbon;' : '',
            'Fk'        => $aField['belongsto'].$this->buildForeignFunction($aRelations)
        ]);
    }
    
    /**
     * Build the code for given fields.
     * 
     * @param string $sId
     * @param array $aColumns
     * 
     * @return string $sField
     */
    protected function buildFields(&$sId, $aColumns)
    {
        $aFillable  = [];
        $aDate      = [
            'function' => ''
        ];
        $sBelongsTo = '';
        
        foreach ($aColumns as $aColumn)
        {
            $this->checkForPrimaryKey($sId, $aColumn);
            
            if(!in_array($aColumn['field'], $this->aExclude))
            {
                $aFillable[] = "'".$aColumn['field']."'";
                
                $this->checkForDate($aDate, $aColumn);
                $this->checkForBelongsTo($sBelongsTo, $aColumn);
            }
        }  
        
        $sDateField = isset($aDate['field']) ? $this->getDateArray($aDate['field'])."\n\n" : '';
        
        return [
            'fillable'  => implode(",\n\t\t", $aFillable),
            'date'      => $sDateField.$aDate['function'],
            'belongsto' => $sBelongsTo,
        ];
    }
    
    /**
     * Build the code for given relation.
     * 
     * @param string $sField
     * @param array $aRelations
     * 
     * @return void
     */
    protected function buildForeignFunction($aRelations)
    {
        $sFunction      = '';
        
        foreach($aRelations as $aRelation)
        {
            if(array_key_exists('pivot', $aRelation))
            {
                $sFunction .= $this->getFunctionBelongsToMany($aRelation)."\n\n";
            }
            else
            {
                $sFunction .= $this->getFunctionHasMany($aRelation)."\n\n";
            }
        }
        
        return $sFunction;
    }
    
    /**
     * Check if the current column is the primary key.
     * 
     * @param int $iId
     * @param array $aColumn
     * 
     * @return void 
     */
    protected function checkForPrimaryKey(&$sId, $aColumn)
    {
        if ($aColumn['key'] == 'PRI')
        {
            $sId                = $aColumn['field'];
            $this->aExclude[]   = $aColumn['field'];
        }
    }
    
    /**
     * Check if the current column is a date.
     * 
     * @param array $aDate
     * @param array $aColumn
     * 
     * @return void 
     */
    protected function checkForDate(&$aDate, $aColumn)
    {
        if($aColumn['type'] == 'date')
        {
            $aDate['field'][]   = "'".$aColumn['field']."'";
            $aDate['function']  .= static::getFunctionDate($aColumn['field'])."\n";
        }
    }
    
    protected function checkForBelongsTo(&$sBelongsTo, $aColumn)
    {
        if($aColumn['key'] == 'FK')
        {  
            $sBelongsTo .= $this->getFunctionBelongsTo($aColumn)."\n\n";
        }
    }
    
    /**
     * Set a given stub in the stub array.
     * 
     * @param string $sName
     * 
     * @return void
     */
    protected function getSpecificStub($sName)
    {
        if (!isset($this->aStubs[$sName]))
        {
            $this->aStubs[$sName] = File::get(
                base_path().static::$STUB_DIR.$sName.'.stub'
            );
        }
        
        return $this->aStubs[$sName];
    }
    
    protected function getDateArray($aField)
    {
        $sStub = $this->getSpecificStub('datearray');
        $sStub = str_replace('DummyDateField', implode(",\n\t\t", $aField), $sStub);
        
        return $sStub;
    }
    
    protected function getFunctionDate($sField)
    {
        $sName = ucfirst(camel_case($sField));
        
        $sStub = $this->getSpecificStub('date');
        $sStub = str_replace('DummyName', $sName, $sStub);
        $sStub = str_replace('DummyField', $sField, $sStub);
        
        return $sStub;
    }
    
    public function getFunctionBelongsTo($aColumn)
    {
        $sModel     = ucfirst(camel_case($aColumn['tableFk']));
        
        $sStub = $this->getSpecificStub('belongsto');
        $sStub = str_replace('DummyFunction', $aColumn['tableFk'], $sStub);
        $sStub = str_replace('DummyModel', $sModel, $sStub);
        $sStub = str_replace('DummyField', $aColumn['field'], $sStub);
        
        return $sStub;
    }
    
    public function getFunctionBelongsToMany($aRelation)
    {
        $sModel     = ucfirst(camel_case($aRelation['related']));
        
        $sStub = $this->getSpecificStub('belongstomany');
        $sStub = str_replace('DummyFunction', $aRelation['related'], $sStub);
        $sStub = str_replace('DummyModel', $sModel, $sStub);
        $sStub = str_replace('DummyPivot', $aRelation['pivot'], $sStub);
        $sStub = str_replace('DummyFk', $aRelation['fk'], $sStub);
        $sStub = str_replace('DummyRelatedFk', $aRelation['fk_related'], $sStub);
        
        return $sStub;
    }
    
    public function getFunctionHasMany($aRelation)
    {
        $sModel     = ucfirst(camel_case($aRelation['related']));
        
        $sStub = $this->getSpecificStub('hasmany');
        $sStub = str_replace('DummyFunction', $aRelation['related'], $sStub);
        $sStub = str_replace('DummyModel', $sModel, $sStub);
        $sStub = str_replace('DummyField', $aRelation['fk'], $sStub);
        
        return $sStub;
    }
}