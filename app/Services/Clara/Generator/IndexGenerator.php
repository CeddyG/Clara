<?php

namespace App\Services\Clara\Generator;

class IndexGenerator extends BaseGenerator
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
    static $STUB = 'index';

    /**
     * Column to exclude.
     * 
     * @var array 
     */
    protected $aExclude = ['id', 'password', 'created_at', 'updated_at'];

    /**
     * Generate the file.
     * 
     * @return void
     */
    public function generate($sFolder, $aColumns)
    {
        $sId        = 'id';
        $aFields    = [
            'head' => [],
            'body' => []
        ];
        
        $this->buildFields($aFields, $sId, $aColumns);
        
        self::createFile($sFolder.'/index.blade.php', [
            'ColName'   => $aFields['head'],
            'Col'       => $aFields['body'],
            'Id'        => $sId,
            'Path'      => $sFolder
        ]);
    } 
    
    /**
     * Check if the current column is the primary key.
     * 
     * @param int $iId
     * @param array $aColumn
     * 
     * @return void 
     */
    private function checkForKey(&$sId, $aColumn)
    {
        if ($aColumn['key'] != '')
        {
            if ($aColumn['key'] == 'PRI')
            {
                $sId = $aColumn['field'];
            }
            
            $this->aExclude[] = $aColumn['field'];
        }
    }  
    
    /**
     * Build a table with the first three columns.
     * 
     * @param array $aFields
     * @param string $sId
     * @param array $aColumns
     * 
     * @return void
     */
    private function buildFields(&$aFields, &$sId, $aColumns)
    {
        $i = 0;
        
        foreach ($aColumns as $aColumn)
        {
            $this->checkForKey($sId, $aColumn);
            
            if(!in_array($aColumn['field'], $this->aExclude))
            {
                $this->getField($aFields, $aColumn);
                $i++;
            }
            
            if ($i == 3)
            {
                break;
            }
        }
        
        $aFields['head'] = implode("\r\t\t", $aFields['head']);
        $aFields['body'] = implode("\r\t\t", $aFields['body']);
    }
    
    /**
     * Get the code for a given column.
     * 
     * @param string $aFields
     * @param array $aColumn
     * 
     * @return void
     */
    private function getField(&$aFields, $aColumn)
    {
        switch ($aColumn['type'])
        {
            case"string":
            case"smallint": 
            case"integer":
            case"bigint":
            case"decimal":
            case"float":  
            case"date":
                $aFields['head'][] = '    <th>'. str_replace('_', ' ', ucfirst($aColumn['field'])) .'</th>';
                $aFields['body'][] = '    { \'data\': \''. $aColumn['field'] .'\' },';
                break;
        }
    }
}