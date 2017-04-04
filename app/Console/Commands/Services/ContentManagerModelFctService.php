<?php

namespace App\Console\Commands\Services;

/**
 * Description of SimpleModelFctService
 *
 * @author alexandre
 */
class ContentManagerModelFctService {
    
    /*
     * Liste des méthodes à ajouter dans le modèle en fonction des champs
     */
    public static function getFctDate($col)
    {
        $name = "";
        $nameTmp = explode('_', $col['field']);
        
        foreach($nameTmp as $tmp)
        {
            $name .= ucfirst($tmp);
        }
        
        $fct = 'public function set'. $name .'Attribute($value)
    {
        $this->attributes[\''. $col['field'] .'\'] = $value ? $value : NULL;
    }

    public function get'. $name .'Attribute($value)
    {
        return Carbon::parse($value)->format(\'d/m/Y\');
    }';
        
        return $fct;
    }
    
    public static function getFctFk($col, $fkTab, $fkTabModel)
    {
        $fkTabModel = self::formateModelName($fkTabModel);
        
        $fct = 'public function '. $fkTab .'()
    {
        return $this->belongsTo(\'App\\'. $fkTabModel .'\', \''. $col['field'] .'\');
    }

    ';
        
        return $fct;
    }
    
    public static function getFctManyToMany($fk, $tab, $pivot, $fkRelated, $fkTabModel)
    {
        $fkTabModel = self::formateModelName($fkTabModel);
        
        $fct = 'public function '. $tab .'()
    {
        return $this->belongsToMany(\'App\\'. $fkTabModel .'\', \''. $pivot .'\', \''. $fk .'\', \''. $fkRelated .'\');
    }

    ';
        
        return $fct;
    }
    
    public static function getFctHasMany($fk, $fkTab, $fkTabModel)
    {
        $fkTabModel = self::formateModelName($fkTabModel);
        
        $fct = 'public function '. $fkTab .'()
    {
        return $this->hasMany(\'App\\'. $fkTabModel .'\', \''. $fk .'\');
    }

    ';
        
        return $fct;
    }
    
    public static function getFctForeign($with)
    {
        $fct = '/**
    * Concatène les relations avec le modèle actuelle, pour les envoyer au controller
    * pour les utiliser dans la vue
    * 
    */
    public function foreign() 
    {
        return $this->with('. implode(',', $with) .')->get();
    }

    ';
        
        return $fct;
    }
    
    public static function getFctListeForeign($fkTabList)
    {
        $fct = '/**
    * Créer une tableau avec les tables étrangères possible, pour alimenter les select dans les formulaires.
    * 
    */
    public function listeForeign()
    {
        return array( '. $fkTabList .' );
    }

    ';
        
        return $fct;
    }
    
    public static function getTabDate($dateField)
    {
        $dateField = implode(',', $dateField);
        
        $tab = '/**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
        '. $dateField .'
    ];

    ';
        
        return $tab;
    }
    
    /*
     * Liste des méthodes à ajouter dans le repository en fonction des relations
     */
    public static function getFctSetForeign($setForeign)
    {
        $fct = '/**
    * Retire et ajoute les liens dans les tables de liaisons
    * pour les relations n:n
    * 
    */
    public function setForeign($objet, $inputs)
    {
        '. $setForeign .'
    }

    ';
        
        return $fct;
    }
    public static function getFctDelForeign($delForeign)
    {
        $fct = '/**
    * Retire les liens dans les tables de liaisons
    * pour les relations n:n avant la suppression de l\'objet
    * 
    */
    public function delForeign($objet)
    {
        '. $delForeign .'
    }

    ';
        
        return $fct;
    }
    
    protected static function formateModelName($fkTabModel)
    {
        $fkTabModelTmp = explode('_', $fkTabModel);
        
        for($i = 0 ; count($fkTabModelTmp) > $i ; $i++)
        {
            $fkTabModelTmp[$i] = ucfirst($fkTabModelTmp[$i]);
        }
        
        return implode('', $fkTabModelTmp);
    }
}
