<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Dataflow extends Model
{
    protected $table = 'dataflow';
    protected $primaryKey = 'id_dataflow';

    public $timestamps = true;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
		'token',
		'repository',
		'separator_csv',
		'columns',
		'where_clause'
    ];
    


}

