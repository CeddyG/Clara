<?php

namespace App\Services\Clara\Generator;

use File;

abstract class BaseGenerator
{    
    /*
     * Path where to generate the file.
     * 
     * @var string
     */
    static $PATH;
    
    /*
     * Stub's name to use to create the file.
     * 
     * @var string
     */
    static $STUB;
    
    /**
     * Get the stub.
     * 
     * @return string
     */
    protected static function getStub()
    {
        return base_path().'/resources/stubs/'. static::$STUB .'.stub';
    }
    
    /**
     * Build the directory for the class if necessary.
     *
     * @param  string $sFileName
     * 
     * @return string $sPath
     */
    protected static function makeDirectory($sFileName)
    {
        $sPath = base_path().static::$PATH.$sFileName;
            
        if (!File::isDirectory(dirname($sPath))) 
        {
            File::makeDirectory(dirname($sPath), 0777, true, true);
        }
        
        return $sPath;
    }
    
    /**
     * Build the content of the file in replace the dummies.
     * 
     * @param array $aDummies Array to contains all dummies with their values.
     * 
     * @return string $sStub Content of the file.
     */
    protected static function buildFile($aDummies)
    {
        $sStub = File::get(self::getStub());
        
        foreach ($aDummies as $sDummy => $sValue)
        {
            $sStub = str_replace('Dummy'.$sDummy, $sValue, $sStub);
        }
        
        return $sStub;
    }
    
    /**
     * Create the file and the directory if needed.
     * 
     * @param string $sFileName 
     * @param array $aDummies Array to contains all dummies with their values.
     * 
     * @return void
     */
    protected static function createFile($sFileName, $aDummies)
    {
        $sPath = self::makeDirectory($sFileName);
        
        File::put($sPath, self::buildFile($aDummies));
    }
}
