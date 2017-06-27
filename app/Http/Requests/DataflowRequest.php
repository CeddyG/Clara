<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DataflowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    public function all()
    {
        $attributes = parent::all();

        if (is_array($attributes['separator_csv']))
        {
            $attributes['separator_csv'] = json_encode([
                'colonne'   => $attributes['separator_csv'][0],
                'ligne'     => $attributes['separator_csv'][1],
                'texte'     => $attributes['separator_csv'][2]
            ]);
        }

        if (is_array($attributes['column-head']) && is_array($attributes['column-column']))
        {
            foreach ($attributes['column-head'] as $i => $shead)
            {
                if ($shead == '')
                {
                    unset($attributes['column-head'][$i]);
                    unset($attributes['column-column'][$i]);
                }
            }
            
            $attributes['columns'] = json_encode(
                array_combine($attributes['column-head'], $attributes['column-column'])
            );
        }
        
        if (
            is_array($attributes['where_clause_column']) 
            && is_array($attributes['where_clause_operator'])
            && is_array($attributes['where_clause_value'])
        )
        {
            $attributes['where_clause'] = [];
            
            foreach ($attributes['where_clause_column'] as $i => $sColumn)
            {
                if (
                    $attributes['where_clause_column'][$i] != ''
                    && $attributes['where_clause_operator'][$i] != ''
                    && $attributes['where_clause_value'][$i] != ''
                )
                {
                    $attributes['where_clause'][] = [
                        $attributes['where_clause_column'][$i],
                        $attributes['where_clause_operator'][$i],
                        $attributes['where_clause_value'][$i]
                    ];
                }
            }
            
            $attributes['where_clause'] = json_encode($attributes['where_clause']);
        }

        return $attributes;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_dataflow'   => 'numeric',
            'name'          => 'string|max:60',
            'token'         => 'string|max:45',
            'repository'    => 'string|max:90',
            'separator_csv' => 'string|max:90',
            'columns'       => '',
            'where_clause'  => ''
        ];
    }
}

