<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug'];
    
    /* DECOMMENTER LES METHODES SUIVANTES EN FONCTION DES BESOINS ET DES RELATIONS */
    
    /**
     * Récupérer les relations n:n
     *
     * Exemple avec la table 'tag', 'article' et leur table de liaison 'article_tag'
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'role_users', 'user_id', 'role_id');
    }
    
    /**
     * Récupérer les relations 1:n
     *
     * Exemple avec la table 'categorie'
     */
    /*public function categories()
    {
        return $this->belongsTo('App\CategorieArticle', 'fk_categorie_article');
    }*/
    
    /**
     * Concatène les relations avec le modèle actuelle, pour les envoyer au controller
     * pour les utiliser dans la vue
     * 
     */
    public function foreign() 
    {
        return $this->with('users')->get();
    }
    
    /**
     * Créer une tableau avec les tables étrangères possible, pour alimenter les select dans les formulaires.
     * 
     */
    public function listeForeign()
    {
        return array( 'users' => User::all()->lists('last_name', 'id'));
    }
}

