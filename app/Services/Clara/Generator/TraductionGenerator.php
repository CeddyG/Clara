<?php

namespace App\Services\Clara\Generator;

class TraductionGenerator extends BaseGenerator
{
    /*
     * Path where to generate the file.
     * 
     * @var string
     */
    static $PATH = '/resources/lang/';
    
    /*
     * Stub's name to use to create the file.
     * 
     * @var string
     */
    static $STUB = 'trad';
    
    /**
     * Generate the file.
     * 
     * @return void
     */
    public function generate($sTable, $sFolder, $aColumns)
    {
        $aTrad  = ["    '". $sTable ."' => '". ucfirst(str_replace('_', ' ', $sTable)) ."'"];
        $sClass = str_replace('_', ' ', $sTable);
        
        foreach ($aColumns as $aColumn)
        {
            if($aColumn['key'] != 'PRI')
            {
                $sTmpTrad = str_replace('id_', '', $aColumn['field']);
                $sTmpTrad = str_replace('fk_', '', $sTmpTrad);
                $sTmpTrad = str_replace('_id', '', $sTmpTrad);
                $sTmpTrad = str_replace('_fk', '', $sTmpTrad);
                $sTmpTrad = str_replace('_', ' ', $sTmpTrad);
                
                $aTrad[] = "    '". $aColumn['field'] ."' => '". ucfirst($sTmpTrad) ."'";
            }
        }
        
        $sTrad = implode(",\n", $aTrad);
        
        self::createFile('en/'.$sFolder.'.php', [
            'Class' => ucfirst($sClass),
            'Trad'  => $sTrad
        ]);
        
        self::createFile('fr/'.$sFolder.'.php', [
            'Class' => ucfirst($sClass),
            'Trad' => $sTrad
        ]);
    }
}
