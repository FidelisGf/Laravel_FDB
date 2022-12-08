<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreProdutoValidator extends FormRequest
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
        if(!Request::has('edit')){
            $rules['MATERIAIS'] = 'required';
            $rules['quantidade_inicial'] = 'required|min:0';
        }
        $rules['NOME'] = 'required|max:60|min:2';
        $rules['DESC'] =  'required|max:120|min:4';
        $rules['VALOR'] = 'required|min:0';
        $rules['ID_CATEGORIA'] = 'required';
        $rules['ID_MEDIDA'] = 'required';
        return $rules;
    }
}
