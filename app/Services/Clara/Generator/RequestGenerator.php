<?php

namespace App\Services\Clara\Generator;

class RequestGenerator extends BaseGenerator
{
    /*
     * Path where to generate the file.
     * 
     * @var string
     */
    static $PATH = '/app/Http/Requests/';
    
    /*
     * Stub's name to use to create the file.
     * 
     * @var string
     */
    static $STUB = 'Request';
    
    /**
     * Generate the file.
     * 
     * @return void
     */
    public function generate($sName, $aColumns)
    {
        $aRules = $this->getRules($aColumns);
        
        self::createFile($sName.'Request.php', [
            'Class' => $sName.'Request',
            'Rules' => implode(",\r\t", $aRules)
        ]);
    }
    
    /**
     * Build the rules for all columns.
     * 
     * @param array $aColumns
     * 
     * @return array $aRules
     */
    private function getRules($aColumns)
    {
        $aRules = [];
        foreach ($aColumns as $aColumn)
        {
            switch ($aColumn['type'])
            {
                case"text":
                case"blob":    
                    $sRule = "";
                    break; 

                case"string":
                    $sLength = ($aColumn['length'] != "") ? "|max:".$aColumn['length'] : "";
                    $sRule = "string".$sLength;
                    break;

                case"smallint": 
                case"integer":
                case"bigint":
                case"decimal":
                case"float":  
                    $sRule = "numeric";
                    break;

                case"date":   
                    $sRule = "date_format:Y-m-d";
                    break;

                case"boolean":    
                    $sRule = "boolean";
                    break;

                default:
                    $sRule = "string";
                    break; 
            }
            
            $aRules[] = "    '".$aColumn['field']."' => '".$sRule."'";
        }
        
        return $aRules;
    }
}
