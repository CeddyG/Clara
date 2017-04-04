<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'host'              => 'required|string',
            'driver'            => 'required|string',
            'port' 		=> 'required|integer',
            'database' 		=> 'required|string',
            'username' 		=> 'required|string'
        ];
    }
}
