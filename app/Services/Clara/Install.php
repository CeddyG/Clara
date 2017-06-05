<?php

namespace App\Services\Clara;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use Illuminate\Support\Facades\Artisan;

class Install
{
    public static function storeBdd($aInputs)
    {               
        self::setEnvFile($aInputs);
        self::changeDatabase($aInputs);	
        
        //Run database migration for Sentinel
        Artisan::call('migrate', ['--database' => 'mysql-tmp']);
    }
    
    private static function setConnectionParams($aInputs)
    {
        return [
            'dbname' 	=> $aInputs['database'],
            'user' 	=> $aInputs['username'],
            'password' 	=> $aInputs['db_password'],
            'host' 	=> $aInputs['host'],
            'port' 	=> $aInputs['port'],
            'driver' 	=> 'pdo_mysql',
        ];
    }
    
    public static function setEnvFile($aInputs)
    {
        $aConnectionParams  = self::setConnectionParams($aInputs);

        // Test database connection
        $oConnection = DriverManager::getConnection($aConnectionParams, new Configuration());
        $oConnection->connect();

        // Change .env file
        $sEnvFile = \Storage::disk('base')->get('.env');

        $sEnvFile = str_replace('DB_HOST=127.0.0.1'	, 'DB_HOST='.$aInputs['host'], $sEnvFile);
        $sEnvFile = str_replace('DB_PORT=3306'		, 'DB_PORT='.$aInputs['port'], $sEnvFile);
        $sEnvFile = str_replace('DB_DATABASE=homestead'	, 'DB_DATABASE='.$aInputs['database'], $sEnvFile);
        $sEnvFile = str_replace('DB_USERNAME=homestead'	, 'DB_USERNAME='.$aInputs['username'], $sEnvFile);
        $sEnvFile = str_replace('DB_PASSWORD=secret'	, 'DB_PASSWORD='.$aInputs['db_password'], $sEnvFile);

        \Storage::disk('base')->put('.env', $sEnvFile);
    }
    
    private static function changeDatabase($aInputs)
    {
        // Change database config
        $oConfig                = config('database.connections');
	$oConfig['mysql-tmp']   = [
            'driver'    => 'mysql',
            'host'      => $aInputs['host'],
            'port'      => $aInputs['port'],
            'database'  => $aInputs['database'],
            'username'  => $aInputs['username'],
            'password'  => $aInputs['db_password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
            'engine'    => null,
        ];
        
        config(['database.connections' => $oConfig]);
    }
}
