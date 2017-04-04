<?php

namespace App\Services\Clara\Generator;

class ControllerGenerator extends BaseGenerator
{
    /*
     * Path where to generate the file.
     * 
     * @var string
     */
    static $PATH = '/app/Http/Controllers/Admin/';
    
    /*
     * Stub's name to use to create the file.
     * 
     * @var string
     */
    static $STUB = 'Controller';
    
    /**
     * Generate the file.
     * 
     * @return void
     */
    public function generate($sName, $sFolder)
    {
        self::createFile($sName.'Controller.php', [
            'Class'         => $sName.'Controller',
            'Repository'    => $sName.'Repository',
            'Request'       => $sName.'Request',
            'Path'          => strtolower($sFolder),
            'Name'          => $sName
        ]);
    }
}
