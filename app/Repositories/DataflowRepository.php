<?php

namespace App\Repositories;

use CeddyG\QueryBuilderRepository\QueryBuilderRepository;

class DataflowRepository extends QueryBuilderRepository
{
    protected $sTable = 'dataflow';

    protected $sPrimaryKey = 'id_dataflow';

    protected $aFillable = [
        'name',
		'token',
		'repository',
		'separator_csv',
		'columns',
		'heads',
		'where_clause'
    ];
    
    public function getSeparatorCsvAttribute($oItem)
    {
        return json_decode($oItem->separator_csv, true);
    }
    
    public function getColumnsAttribute($oItem)
    {
        return json_decode($oItem->columns, true);
    }
    
    public function getWhereClauseAttribute($oItem)
    {
        return json_decode($oItem->where_clause, true);
    }
    
    /**
     * Create a record.
     * 
     * @param array $aAttributes
     * 
     * @return bool|int if multiple return bool, if simple return the ID.
     */
    public function create(array $aAttributes)
    {
        if(is_array(array_values($aAttributes)[0]))
        {
            foreach ($aAttributes as $aAttribute)
            {
                $aAttribute['token'] = substr(sha1(uniqid()), 0, 30);
            }
            
            return parent::create($aAttributes);
        }
        else
        {
            $aAttributes['token'] = substr(sha1(uniqid()), 0, 30);
            
            return parent::create($aAttributes);
        }
    }
}
