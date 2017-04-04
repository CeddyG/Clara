<?php

namespace App\Services\Clara\Generator;

use File;

class FormGenerator extends BaseGenerator
{
    /*
     * Path where to generate the file.
     * 
     * @var string
     */
    static $PATH = '/resources/views/admin/';
    
    /*
     * Stub's name to use to create the file.
     * 
     * @var string
     */
    static $STUB = 'form';
    
    /**
     * Directory for specific stubs.
     * 
     * @var string
     */
    private static $STUB_DIR = '/resources/stubs/form/';

    /**
     * Column to exclude.
     * 
     * @var array 
     */
    protected $aExclude = ['id', 'password', 'created_at', 'updated_at'];
    
    /**
     * If we need ckeditor.
     * 
     * @var bool 
     */
    protected $bCkeditor = false;
    
    /**
     * If we need datepicker.
     * 
     * @var bool 
     */
    protected $bDatepicker = false;
    
    /**
     * If we need select2.
     * 
     * @var bool 
     */
    protected $bSelect2 = false;
    
    /**
     * If we need select2.
     * 
     * @var bool 
     */
    protected $bICheck = false;

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
    public function generate($sTable, $sFolder, $aColumns, $aRelations)
    {
        $sId    = 'id';
        $sField = $this->buildFields($sId, $sTable, $aColumns);
        
        $this->buildFieldsFromRelations($sField, $aRelations);
        
        $sJs    = $this->getJs();
        $sCss   = $this->getCss();
        
        self::createFile($sFolder.'/form.blade.php', [
            'CSS'   => $sCss,
            'Path'  => strtolower($sFolder),
            'Id'    => $sId,
            'Field' => $sField,
            'JS'    => $sJs
        ]);
    }
    
    /**
     * Build the code for given fields.
     * 
     * @param string $sId
     * @param string $sTable
     * @param array $aColumns
     * 
     * @return string $sField
     */
    private function buildFields(&$sId, $sTable, $aColumns)
    {
        $sField = '';
        
        foreach ($aColumns as $aColumn)
        {
            $this->checkForPrimaryKey($sId, $aColumn);
            
            if(!in_array($aColumn['field'], $this->aExclude))
            {
                $this->getField($sField, $sTable, $aColumn);
            }
        }  
        
        return $sField;
    }
    
    /**
     * Build the code for given relation.
     * 
     * @param string $sField
     * @param array $aRelations
     * 
     * @return void
     */
    private function buildFieldsFromRelations(&$sField, $aRelations)
    {
        foreach($aRelations as $aRelation)
        {
            if(array_key_exists('pivot', $aRelation))
            {
                $sField .= $this->getFieldSelect2(
                    $aRelation['related'], 
                    $aRelation['related'], 
                    $this->getSelectedOptions($aRelation['related'], $aRelation['id_related'], $aRelation['name_field'], true),
                    "\n\t\t\t    ".$this->getSelectedOptionsMultiple($aRelation['related'], $aRelation['id_related']),
                    "\n\t\t\t    ->multiple()", 
                    $aRelation['name_field'],
                    "{!! BootForm::hidden('DummyTable')->value('') !!}")
                ."\n";
                
                $this->bSelect2 = true;
            }
        }
    }
    
    /**
     * Check if the current column is the primary key.
     * 
     * @param int $iId
     * @param array $aColumn
     * 
     * @return void 
     */
    private function checkForPrimaryKey(&$sId, $aColumn)
    {
        if ($aColumn['key'] == 'PRI')
        {
            $sId = $aColumn['field'];
            $this->aExclude[] = $aColumn['field'];
        }
    }
    
    /**
     * Get the code for a given column.
     * 
     * @param string $sField
     * @param string $sTable
     * @param array $aColumn
     * 
     * @return void
     */
    private function getField(&$sField, $sTable, $aColumn)
    {
        if($aColumn['key'] == 'FK')
        {
            $sField .= $this->getFieldFk($aColumn)."\n";
        }
        else
        {
            $this->switchField($sField, $sTable, $aColumn);
        }
    }
    
    /**
     * Get the code for a given column with his type.
     * 
     * @param string $sField
     * @param string $sTable
     * @param array $aColumn
     * 
     * @return void
     */
    private function switchField(&$sField, $sTable, $aColumn)
    {
        switch ($aColumn['type'])
        {
            case"text":
            case"blob":    
                $sField .= $this->getFieldTextArea($sTable, $aColumn['field'])."\n";
                $this->bCkeditor = true;
                break;

            case"boolean":    
                $sField .= $this->getFieldCheck($sTable, $aColumn['field'])."\n";
                $this->bICheck = true;
                break;

            case"date":    
                $sField .= $this->getFieldDate($sTable, $aColumn['field'])."\n";
                $this->bDatepicker = true;
                break;

            default:
                $sField .= $this->getFieldText($sTable, $aColumn['field'])."\n";
                break;
        }
    }
    
    private function getSelectedOptions($sTable, $sValue, $sLabel, $bMultiple)
    {
        if ($bMultiple)
        {
            return '$oItem->'.$sTable.'->pluck(\''.$sLabel.'\', \''.$sValue.'\')->toArray()';
        }
        else
        {
            return '[$oItem->'.$sValue.' => $oItem->'.$sTable.'->'.$sLabel.']';
        }
    }
    
    private function getSelectedOptionsMultiple($sTable, $sValue)
    {
        return '->select($oItem->'.$sTable.'->pluck(\''.$sValue.'\')->toArray())';
    }
    
    private function getJs()
    {
        $sJs = ($this->bDatepicker) ? $this->getJsDatepicker()."\n\n" : "";
        $sJs .= ($this->bSelect2)   ? $this->getJsSelect2()."\n\n" : "";
        $sJs .= ($this->bICheck)   ? $this->getJsICheck()."\n\n" : "";
        $sJs .= ($this->bCkeditor)  ? $this->getJsCkeditor()."\n\n" : "";
        $sJs = ($sJs != "") ? "@section('JS')\n"
            . $sJs
            . "@stop" 
            : "";
        
        return $sJs;
    }
    
    private function getCss()
    {
        $sCss = ($this->bDatepicker)    ? $this->getCssDatepicker()."\n" : "";
        $sCss .= ($this->bSelect2)      ? $this->getCssSelect()."\n" : "";
        $sCss .= ($this->bICheck)        ? $this->getCssICheck()."\n\n" : "";
        $sCss = ($sCss != "") ? "@section('CSS')\n"
            . $sCss
            . "@stop" 
            : "";
        
        return $sCss;
    }
    
    /**
     * Set a given stub in the stub array.
     * 
     * @param string $sName
     */
    private function getSpecificStub($sName)
    {
        if (!isset($this->aStubs[$sName]))
        {
            $this->aStubs[$sName] = File::get(
                base_path().self::$STUB_DIR.$sName.'.stub'
            );
        }
        
        return $this->aStubs[$sName];
    }
    
    private function replaceTableAndName($sStubName, $sTable, $sName)
    {
        $sStub = $this->getSpecificStub($sStubName);
        $sStub = str_replace('DummyTable', str_replace('_', '-', $sTable), $sStub);
        $sStub = str_replace('DummyName', $sName, $sStub);
        
        return $sStub;
    }

    private function getFieldText($sTable, $sName)
    {
        return $this->replaceTableAndName('text', $sTable, $sName);
    }
    
    private function getFieldTextArea($sTable, $sName)
    {
        return $this->replaceTableAndName('textarea', $sTable, $sName);
    }
    
    private function getFieldDate($sTable, $sName)
    {
        return $this->replaceTableAndName('date', $sTable, $sName);
    }
    
    private function getFieldCheck($sTable, $sName)
    {
        return $this->replaceTableAndName('check', $sTable, $sName);
    }
    
    private function getFieldSelect2(
        $sName, 
        $sTable, 
        $sOptions, 
        $sSelected, 
        $sMultiple, 
        $sNameField,
        $sHidden = ''
    )
    {
        $sFolder = str_replace('_', '-', $sTable);
        
        $sStub = $this->getSpecificStub('select2');
        $sStub = str_replace('DummyFolder', $sFolder, $sStub);
        $sStub = str_replace('DummyHidden', $sHidden, $sStub);
        $sStub = str_replace('DummyTable', $sTable, $sStub);
        $sStub = str_replace('DummyOptions', $sOptions, $sStub);
        $sStub = str_replace('DummySelected', $sSelected, $sStub);
        $sStub = str_replace('DummyMultiple', $sMultiple, $sStub);
        $sStub = str_replace('DummyField', $sNameField, $sStub);
        
        return str_replace('DummyName', $sName, $sStub);
    }
    
    private function getFieldFk($aColumn)
    {
        $this->bSelect2 = true;
        
        return $this->getFieldSelect2(
            $aColumn['field'], 
            $aColumn['tableFk'], 
            $this->getSelectedOptions($aColumn['tableFk'], $aColumn['field'], $aColumn['name_field'], false),
            '',
            '', 
            $aColumn['name_field']);
    }
    
    private function getJsDatepicker()
    {
        return $this->getSpecificStub('datepicker');
    }
    
    private function getJsCkeditor()
    {
        return $this->getSpecificStub('ckeditor');
    }
    
    private function getJsSelect2()
    {
        return $this->getSpecificStub('select2js');
    }
    
    private function getJsICheck()
    {
        return $this->getSpecificStub('icheckjs');
    }
    
    private function getCssDatepicker()
    {
        return $this->getSpecificStub('datepickercss');
    }
    
    private function getCssSelect()
    {
        return $this->getSpecificStub('select2css');
    }
    
    private function getCssICheck()
    {
        return $this->getSpecificStub('icheckcss');
    }
}
