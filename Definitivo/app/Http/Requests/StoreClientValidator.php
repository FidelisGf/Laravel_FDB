<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientValidator extends FormRequest
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
            'NOME' => ['required', 'max:60', 'min:2'],
            'CPF' => ['required', 'min:11', 'max:11'],
            'ENDERECO'=> ['required', 'min:8'],
            'EMAIL'=> ['required', 'min:12'],
            'TELEFONE' => ['required', 'min:11', 'max:12']
        ];
    }
}
