<?php

namespace App\Console\Commands;

use App\Console\Commands\ContentManagerControllerCommand;
use DB;

class ContentManagerControllerGlobalCommand extends ContentManagerControllerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:cm:global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new Content Manager model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->connection();
        
        $json = file_get_contents(base_path()."/content_manager.json");
        
        if($json !== false)
        {
            $tab = json_decode($json, true);

            foreach($tab['tables'] as $r)
            {
                
                if(isset($r['name'])
                && isset($r['table'])
                && isset($r['folder']))
                {
                    //On récupère les arguments
                    $name = $r['name'];
                    $table = $r['table'];
                    $folder = $r['folder'];
                    $many = (isset($r['many'])) ? $r['many'] : array();

                    $this->createContentManager($name, $table, $folder, $many);
                }
                else
                {
                    $this->info('simple.json not formatted correctely.');
                }
            }
        }
        else
        {
            $this->info('simple.json not found.');
        }
    }
    
    protected function connection()
    {
        $env = file(base_path().'/.env');
        
        foreach ($env as $line)
        {
            switch (true)
            {
                case (strpos($line, 'DB_HOST') !== false) :
                    $host = str_replace('DB_HOST=', '', $line);
                    break;
                case (strpos($line, 'DB_PORT') !== false) :
                    $port = str_replace('DB_PORT=', '', $line);
                    break;
                case (strpos($line, 'DB_DATABASE') !== false) :
                    $database = str_replace('DB_DATABASE=', '', $line);
                    break;
                case (strpos($line, 'DB_USERNAME') !== false) :
                    $username = str_replace('DB_USERNAME=', '', $line);
                    break;
                case (strpos($line, 'DB_PASSWORD') !== false) :
                    $password = str_replace('DB_PASSWORD=', '', $line);
                    break;
            }
        }
        
        $host = str_replace("\n", '', $host);
        $port = str_replace("\n", '', $port);
        $database = str_replace("\n", '', $database);
        $username = str_replace("\n", '', $username);
        $password = str_replace("\n", '', $password);
        
        //On change de connexion
        $config = config('database.connections');
        $config['mysql-tmp'] = [
                                        'driver' => 'mysql',
                                        'host' => $host,
                                        'port' => $port,
                                        'database' => $database,
                                        'username' => $username,
                                        'password' => $password,
                                        'charset' => 'utf8',
                                        'collation' => 'utf8_unicode_ci',
                                        'prefix' => '',
                                        'strict' => false,
                                        'engine' => null,
                                    ];
        config(['database.connections' => $config]);
        
        DB::setDefaultConnection('mysql-tmp');
    }
    
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            
        ];
    }
}
