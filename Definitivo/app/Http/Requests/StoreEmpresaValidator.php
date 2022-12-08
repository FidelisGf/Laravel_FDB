<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaValidator extends FormRequest
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
            'NOME' => 'required|unique:EMPRESAS|max:50|min:2',
            'CNPJ' => 'required|min:18|max:18',
            'EMAIL'=> 'required|unique:EMPRESAS|email|max:120|min:5',
            'NOME_FANTASIA' => 'required|max:60|min:2',
            'ENDERECO' => 'required|max:255|min:2',
            'INC_ESTADUAL' => 'required|unique:EMPRESAS|max:12|min:9',
        ];
    }
}
