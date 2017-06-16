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
            'repository'    => 'string|max:45',
            'separator_csv' => 'string|max:90',
            'columns'       => '',
            'where_clause'  => 'string|max:90'
        ];
    }
}

