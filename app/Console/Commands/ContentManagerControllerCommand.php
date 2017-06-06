<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use App\Console\Commands\Services\ContentManagerViewService as SView;
use App\Console\Commands\Services\ContentManagerModelFctService as SFct;

class ContentManagerControllerCommand extends GeneratorCommand
{
    protected $exclude = ['id', 'password', 'created_at', 'updated_at'];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:cm {name} {table} {folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new Content Manager model';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Content Manager model';

    /*
     * Custom attributes
     */

    protected $con;
    protected $database;
    protected $sm;
    protected $relations;
    protected $dummies;


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {       
        //On récupère les arguments
        $name = $this->argument('name');
        $table = $this->argument('table');
        $folder = $this->argument('folder');
        
        $this->createContentManager($name, $table, $folder);
    }
    
    protected function createContentManager($name, $table, $folder, $many = array())
    {        
        // On construit le nom de la table à partir du nom du modèle
        $table = strtolower($table);
        
        //On récupère les colonnes
        $columns = $this->getColumns($table);
        
        //On récupère les relations de la table
        $relations = $this->getRelations($table, $many);
        
        //On génère les dummies
        $this->dummies = $this->getDummies($name, $table, $folder, $columns, $relations);
        
        //On créer les variables pour les fichiers
        $path1 = base_path().'/app/Http/Controllers/Admin/'. $name .'Controller.php';
        $path2 = base_path().'/app/Http/Requests/'. $name .'Request.php';
        $path3 = base_path().'/app/Repositories/'. $name .'Repository.php';
        $path4 = base_path().'/app/'. $name .'.php';
        
        //Idem pour les vue
        $path5 = base_path().'/resources/views/admin/'. $folder .'/index.blade.php';
        $path6 = base_path().'/resources/views/admin/'. $folder .'/form.blade.php';
        
        //Idem pour les trad
        $path7 = base_path().'/resources/lang/en/'. $folder .'.php';
        $path8 = base_path().'/resources/lang/fr/'. $folder .'.php';

        /*if ($this->alreadyExists($name)) {
            $this->info($this->type.' already exists!');

            return false;
        }*/
        
        $this->makeDirectory($path1);
        $this->makeDirectory($path2);
        $this->makeDirectory($path3);
        $this->makeDirectory($path4);
        $this->makeDirectory($path5);
        $this->makeDirectory($path6);
        $this->makeDirectory($path7);
        $this->makeDirectory($path8);
        
        //On créer les fichiers à partir des stubs avec les bonnes valeurs
        $this->files->put($path1, $this->buildClassController($name));
        $this->files->put($path2, $this->buildClassRequest($name));
        $this->files->put($path3, $this->buildClassRepository($name));
        $this->files->put($path4, $this->buildClassModel($name));
        
        //Idem pour les vue
        $this->files->put($path5, $this->buildViewIndex($name));
        $this->files->put($path6, $this->buildViewForm($name));
        
        //Idem pour les trad
        $this->files->put($path7, $this->buildTrad($name));
        $this->files->put($path8, $this->buildTrad($name));

        $this->info($this->type.' for '. $name .' created successfully.');
    }
    
    protected function getColumns($tableName)
    {
        $formattedColumn = array();
        
        //On récupère les infos sur les champs de la table
        
        // single column thru Laravel API
        $con = \DB::connection();
        $con->getDoctrineSchemaManager()
                        ->getDatabasePlatform()
                        ->registerDoctrineTypeMapping('enum', 'string');
        
        $con->getDoctrineSchemaManager()
                        ->getDatabasePlatform()
                        ->registerDoctrineTypeMapping('tinyint', 'smallint');
        $sm = $con->getDoctrineSchemaManager(); // get the underlying Doctrine manager
        $table = $sm->listTableDetails($tableName); // \Doctrine\DBAL\Schema\Table
        $columns = $table->getColumns(); // array of \Doctrine\DBAL\Schema\Column
        
        //On crée un tableau avec les clé étrangère avec leur table correspondante
        $foreignKey = $table->getForeignKeys();
        $arFk = array();
        foreach($foreignKey as $fk)
        {
            $arFk[] = array("column" => $fk->getColumns()[0],
                            "table" => $fk->getForeignTableName());
        }
        
        //Pour chaque colonnes on récupère les infos que nous voulons et on les formatte
        foreach ($columns as $col)
        {
            $ar = array();
            
            $ar['field'] = $col->getName();
            $ar['type'] = $col->getType()->getName();
            $ar['length'] = $col->getLength();
            
            /*
             * On test si le champ est la clé primaire, si c'est le cas on le précise au tableau
             * et on l'ajoute dans les champ à exclure pour la modif.             
             */
            if($table->getPrimaryKey()->getColumns()[0] == $col->getName())
            {
                $ar['key'] = "PRI";
                $this->exclude[] = $col->getName();
            }
            //Sinon on fait de même pour les clés étrangères, sans les exclure, mais on précise la table
            else if(in_array($col->getName(), array_column($arFk, "column")))
            {
                $key = array_search($col->getName(), array_column($arFk, 'column'));
                $ar['tableFk'] = $arFk[$key]["table"];
                $ar['key'] = "FK";
            }
            else
            {
                $ar['key'] = "";
            }
            
            $formattedColumn[] = $ar;
        }
        
        return $formattedColumn;
    }
    
    protected function getRelations($tableName, $many)
    {
        $ar = array();
        
        
        // single column thru Laravel API
        $con = \DB::connection();
        $database = $con->getDatabaseName();
        $relations = \DB::select("select TABLE_NAME,COLUMN_NAME "
                                . "from INFORMATION_SCHEMA.KEY_COLUMN_USAGE "
                                . "where REFERENCED_TABLE_NAME = '". $tableName ."' "
                                . "AND REFERENCED_TABLE_SCHEMA = '". $database ."'");
        
        foreach($relations as $relation)
        {
            $relatedTab = $relation->TABLE_NAME;
            $relatedKey = $relation->COLUMN_NAME;
            
            $key = array_search($relatedTab, array_column($many, 'pivot'));
            
            if($key !== false)
            {
                $ar[] = array("related" => $many[$key]['related'],
                                "fk" => $relatedKey,
                                "pivot" => $relatedTab,
                                "fk_related" => $many[$key]['related_foreign_key']);
            }
            else
            {
                $ar[] = array("related" => $relatedTab,
                                "fk" => $relatedKey);
            }
        }
        
        return $ar;
    }
    
    protected function getDummies($className, $table, $folder, $columns, $relations)
    {
        //Id par défaut
        $id ='id';
        //Variable pour lister les règles sur les différents champ. CONTROLLER
        $rules = "";
        //Variable pour lister les tables en relation n:n, pour la fonction setForeign. REPOSITORY
        $setForeign = "";
        //Idem pour delForeign
        $delForeign = "";
        //Variable pour lister les champs modifiable. MODEL
        $fillable = array();
        //Variable pour lister les champs date. MODEL
        $dateField = array();
        //Variable pour lister les fonctions relatives aux champs de type date. MODEL
        $dateFct = "";
        //Variable pour lister les fonctions relatives aux tables étrangère. MODEL
        $fkTab = "";
        //Variable pour lister les tables étrangères dans les with(), pour la fonction foreign(). MODEL
        $fkWith = array();
        //Variable pour lister les tables dans le tableau de retour pour alimenter les select des formulaires. MODEL
        $fkTabList = "";
        //Variable pour lister les entete du tableau dans la vue d'index. INDEX
        $head = "";
        //Variable pour lister les colonnes du tableau dans la vue d'index. INDEX
        $tabColumn = "";
        //Variable pour lister les champs dans le formulaire. FORM
        $field = "";
        //Variable pour définir si on met CKeditor. FORM
        $ckeditor = false;
        //Variable pour définir si on met un datepicker. FORM
        $datepicker = false;
        //Variable pour définir si on met un selecteur multiple. FORM
        $select2 = false;
        //Variable pour contenir les trad
        $trad = "";
        
        $aFkTabName = [];
        //Iterator pour mettre 3 champs max dans le tableau de l'index
        $i = 0;
        foreach ($columns as $col)
        {
            if(!in_array($col['field'], $this->exclude))
            {
                //On ajoute le champ aux fillable si il n'est pas exclut
                $fillable[] = "'".$col['field']."'";
                
                //On test le type de champ, pour spécifier les règles et si on affiche dans le tableau de l'index
                $ar = $this->switchType($col, $i);
                $head .= $ar['head'];
                $tabColumn .= $ar['tabColumn'];
                $rule = $ar['rule'];
                
                $length = ($rule == "string" && $col['length'] != "") ? "|max:".$col['length'] : "";

                $rules .= "'" . $col['field'] . "' => '". $rule.$length ."',\n"
                        . "            ";
                
                //Si le champ est de type date, on l'ajoute à la liste, ainsi que sa fonction
                if($col['type'] == 'date')
                {
                    $dateField[] = "'".$col['field']."'";
                    $dateFct .= SFct::getFctDate($col);
                }
                
                //On test si le champ est une clé étrangère
                if($col['key'] == 'FK')
                {         
                    //On formatte le nom du champ du tableau qui va contenir la liste de la table étrangère
                    $fkTabName = $this->getTableName($col['tableFk']);
            
                    if(in_array($fkTabName, $aFkTabName))
                    {
                        $i = 1;

                        do
                        {
                            $sNewTabName = $fkTabName.$i;
                            $i++;
                        }
                        while(in_array($sNewTabName, $aFkTabName));

                        $fkTabName = $sNewTabName;
                    }
            
                    $aFkTabName[] = $fkTabName;
                    
                    $fkTab .= SFct::getFctFk($col, $fkTabName, $col['tableFk']);
                    $fkWith[] .= "'". $fkTabName ."'";
                    $fkTabList = $this->getTableList($col['tableFk'], $fkTabName, $fkTabList);
                    
                    $field .= SView::getFieldFk($table, $col);
                }
                else
                {
                    switch ($col['type'])
                    {
                        case"text":
                        case"blob":    
                            $field .= SView::getFieldTextArea($table, $col['field']);
                            $ckeditor = true;
                            break;

                        case"boolean":    
                            $field .= SView::getFieldCheck($table, $col['field']);
                            break;

                        case"date":    
                            $field .= SView::getFieldDate($table, $col['field']);
                            $datepicker = true;
                            break;

                        default:
                            $field .= SView::getFieldText($table, $col['field']);
                            break;
                    }
                }
            }
            
            $id = ($col['key'] == 'PRI') ? $col['field'] : $id;
            
            //Création de la trad du champ
            if($col['key'] != 'PRI')
            {
                $tmpTrad = str_replace('id_', '', $col['field']);
                $tmpTrad = str_replace('fk_', '', $tmpTrad);
                $tmpTrad = str_replace('_id', '', $tmpTrad);
                $tmpTrad = str_replace('_fk', '', $tmpTrad);
                $tmpTrad = str_replace('_', ' ', $tmpTrad);
                
                $trad .= "'". $col['field'] ."' => '". ucfirst($tmpTrad) ."',
    ";
            }
        }
        
        //On créer chaque relation où la clé primaire est propoagée (relation 1:n ou n:n)
        foreach($relations as $relation)
        {
            $fkTabName = $this->getTableName($relation['related']);
            
            if(in_array($fkTabName, $aFkTabName))
            {
                $i = 1;
                
                do
                {
                    $sNewTabName = $fkTabName.$i;
                    $i++;
                }
                while(in_array($sNewTabName, $aFkTabName));
                
                $fkTabName = $sNewTabName;
            }
            
            $aFkTabName[] = $fkTabName;
            
            $fk = $relation['fk'];
            
            if(array_key_exists('pivot', $relation))
            {                
                $pivot = $relation['pivot'];
                $fkRelated = $relation['fk_related'];
                
                $setForeign .= "if(isset(\$inputs['". $fkTabName ."']))\n"
                        . "            \$objet->". $fkTabName ."()->sync(\$inputs['". $fkTabName ."']);\n"
                        . "         else\n"
                        . "            \$objet->". $fkTabName ."()->detach();\n
        ";
                $delForeign .= '$objet->'. $fkTabName .'()->detach();
        ';
                
                $fkTab .= SFct::getFctManyToMany($fk, $fkTabName, $pivot, $fkRelated, $relation['related']);
                
                $fkColumns = $this->getColumns($relation['related']);
                
                $idFk = "id";
                foreach($fkColumns as $fkCol)
                {
                    if($fkCol['key'] == 'PRI')
                    {
                        $idFk = $fkCol['field'];
                        break;
                    }
                }
                    
                $field .= SView::getFieldSelect($fkTabName, $relation['related'], $idFk);
                $select2 = true;
            }
            else
            {
                //A voir pour le cas des images
                $fkTab .= SFct::getFctHasMany($fk, $fkTabName, $relation['related']);
            }
            
            $fkWith[] .= "'". $fkTabName ."'";
            $fkTabList = $this->getTableList($relation['related'], $fkTabName, $fkTabList);
        }
        
        //MODEL
        // CSV format
        $fillable = implode(',', $fillable);

        
        $dateField = (!empty($dateField)) ? SFct::getTabDate($dateField) : '';
        $dateDummy = $dateField.$dateFct;
        
        
        $fkWith = (!empty($fkWith)) ? SFct::getFctForeign($fkWith) : '';
        $fkTabList = ($fkTabList != '') ? SFct::getFctListeForeign($fkTabList) : '';
        $fkDummy = $fkTab.$fkWith.$fkTabList;
        
        //REPOSITORY
        $setForeign = ($setForeign != '') ? SFct::getFctSetForeign($setForeign) : '';
        $delForeign = ($delForeign != '') ? SFct::getFctDelForeign($delForeign) : '';
        
        //FORM
        $js = ($datepicker) ? SView::getJsDatepicker() : "";
        $js .= ($select2) ? SView::getJsSelect() : "";
        $js .= ($ckeditor) ? SView::getJsCkeditor() : "";
        $js = ($js != "") ? "@section('JS')"
                
                . $js
                
                . "@stop" : "";
        
        $css = ($datepicker) ? SView::getCssDatepicker() : "";
        $css .= ($select2) ? SView::getCssSelect() : "";
        $css = ($css != "") ? "@section('CSS')"
                
                . $css
                
                . "@stop" : "";
        
        return ['Request' => $className.'Request',
                    'Repository' => $className.'Repository',
                    'Path' => strtolower($folder),
                    'Name' => $className,
                    'Table' => $table,
                    'SetForeign' => $setForeign,
                    'DelForeign' => $delForeign,
                    'Id' => $id,
                    'Rules' => $rules,
                    'Fillable' => $fillable,
                    'Date' => $dateDummy,
                    'Fk' => $fkDummy,
                    'ColName' => $head,
                    'Col' => $tabColumn,
                    'Field' => $field,
                    'JS' => $js,
                    'CSS' => $css,
                    'Trad' => $trad];
    }
    
    protected function switchType($col, &$i)
    {
        $head = '';
        $tabColumn = '';
        $rule = '';
        
        switch ($col['type'])
        {
            case"text":
            case"blob":    
                $rule = "";
                break; 

            case"string":
                $rule = "string";

                if($col['key'] != "FK" && $i < 3)
                {
                    $head = '<th>'. str_replace('_', ' ', ucfirst($col['field'])) .'</th>
                             ';
                    $tabColumn = '<td>{{ $objet->'. $col['field'] .' }}</td>
                                ';
                    $i++;
                }
                break;
            case"smallint": 
            case"integer":
            case"bigint":
            case"decimal":
            case"float":  
                $rule = "numeric";

                if($col['key'] != "FK" && $i < 3)
                {
                    $head = '<th>'. str_replace('_', ' ', ucfirst($col['field'])) .'</th>
                             ';
                    $tabColumn = '<td>{{ $objet->'. $col['field'] .' }}</td>
                                ';
                    $i++;
                }
                break;

            case"date":   
                $rule = "date_format:Y-m-d";

                if($col['key'] != "FK" && $i < 3)
                {
                    $head = '<th>'. str_replace('_', ' ', ucfirst($col['field'])) .'</th>
                            ';
                    $tabColumn = '<td>{{ $objet->'. $col['field'] .' }}</td>
                                ';
                    $i++;
                }
                break;

            case"boolean":    
                $rule = "boolean";
                break;

            default:
                $rule = "string";
                break; 
        }
        
        return array('head' => $head,
                    'tabColumn' => $tabColumn,
                    'rule' => $rule);
    }
    
    protected function getTableName($name)
    {
        $fkTabName = "";
        $fkFieldTmp = explode('_', $name);

        $i = 0;
        foreach($fkFieldTmp as $tmp)
        {
            $fkTabName .= ($i == 1) ? ucfirst($tmp) : $tmp;
            $i = 1;
        }
        
        return $fkTabName;
    }
    
    protected function getTableList($fkTab, $fkTabName, $fkTabList)
    {
        $columns = $this->getColumns($fkTab);
        
        $id = $columns[0]['field'];
        $name = $columns[0]['field'];
        $idOk = false;
        $nameOk = false;
        
        foreach($columns as $column)
        {
            if($column['key'] == 'PRI')
            {
                $id = $column['field'];
                $idOk = true;
            }
            
            if($column['key'] == '')
            {
                $name = $column['field'];
                $nameOk = true;
            }
            
            if($idOk && $nameOk)
                break;
        }
        
        if($fkTabList != "")
        {
            $fkTabList .= ",
                '". $fkTabName ."' => ". ucfirst($this->getTableName($fkTab)) ."::all()->lists('". $name ."', '". $id ."')";
        }
        else
        {
            $fkTabList .= "'". $fkTabName ."' => ". ucfirst($this->getTableName($fkTab)) ."::all()->lists('". $name ."', '". $id ."')";
        }
        
        return $fkTabList;
    }

    /*
     * Liste des stubs
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/ContentManagerController.stub';
    }
    
    protected function getStubController()
    {
        return __DIR__.'/stubs/ContentManagerController.stub';
    }
    
    protected function getStubRequest()
    {
        return __DIR__.'/stubs/ContentManagerRequest.stub';
    }
    
    protected function getStubRepository()
    {
        return __DIR__.'/stubs/ContentManagerRepository.stub';
    }
    
    protected function getStubModel()
    {
        return __DIR__.'/stubs/ContentManagerModel.stub';
    }
    
    protected function getStubIndex()
    {
        return __DIR__.'/stubs/index.stub';
    }
    
    protected function getStubForm()
    {
        return __DIR__.'/stubs/form.stub';
    }
    
    protected function getStubTrad()
    {
        return __DIR__.'/stubs/trad.stub';
    }

    /*
     * Liste des builder
     * 
     */
    protected function buildClassController($name)
    {
        $stub = $this->files->get($this->getStubController());
        
        return $this->replaceDummies($stub, 'Repository')
                    ->replaceDummies($stub, 'Path')
                    ->replaceDummies($stub, 'Name')
                    ->replaceDummies($stub, 'Request')
                    ->replaceClass($stub, $name.'Controller');
    }
    
    protected function buildClassRequest($name)
    {
        $stub = $this->files->get($this->getStubRequest());
        
        return $this->replaceDummies($stub, 'Rules')
                    ->replaceClass($stub, $name.'Request');
    }
   
    protected function buildClassRepository($name)
    {
        $stub = $this->files->get($this->getStubRepository());
        
        return $this->replaceDummies($stub, 'Name')
                    ->replaceDummies($stub, 'SetForeign')
                    ->replaceDummies($stub, 'DelForeign')
                    ->replaceClass($stub, $name.'Repository');
    }
    
    protected function buildClassModel($name)
    {
        $stub = $this->files->get($this->getStubModel());
        
        return $this->replaceDummies($stub, 'Fillable')
                    ->replaceDummies($stub, 'Table')
                    ->replaceDummies($stub, 'Id')
                    ->replaceDummies($stub, 'Date')
                    ->replaceDummies($stub, 'Fk')
                    ->replaceClass($stub, $name);
    }
    
    //View
    protected function buildViewIndex($name)
    {
        $stub = $this->files->get($this->getStubIndex());
        
        return $this->replaceDummies($stub, 'Path')
                    ->replaceDummies($stub, 'ColName')
                    ->replaceDummies($stub, 'Col')
                    ->replaceDummies($stub, 'Id')
                    ->replaceClass($stub, $name);
    }
    
    protected function buildViewForm($name)
    {
        $stub = $this->files->get($this->getStubForm());
        
        return $this->replaceDummies($stub, 'Path')
                    ->replaceDummies($stub, 'Field')
                    ->replaceDummies($stub, 'JS')
                    ->replaceDummies($stub, 'CSS')
                    ->replaceDummies($stub, 'Id')
                    ->replaceClass($stub, $name);
    }
    
    //Trad
    protected function buildTrad($name)
    {
        $stub = $this->files->get($this->getStubTrad());
        
        return $this->replaceDummies($stub, 'Trad')
                    ->replaceClass($stub, $name);
    }
    
    /*
     * Liste des replacement des dummies
     * 
     */
    
    protected function replaceDummies(&$stub, $idDummy)
    {
        $dummy = $this->dummies[$idDummy];
        
        $stub = str_replace('Dummy'.$idDummy, $dummy, $stub);

        return $this;
    }    

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
            ['table', InputArgument::REQUIRED, 'The name of the table'],
            ['folder', InputArgument::REQUIRED, 'The name of the folder of view'],
        ];
    }
}
