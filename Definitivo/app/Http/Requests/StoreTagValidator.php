<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagValidator extends FormRequest
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
            'NOME' => 'required|max:100|min:2'
        ];
    }
    public function messages()
    {
        return [
            'NOME.required' => ' O nome é obrigatorio',
            'NOME.min' => ' Insira no minimo 2 caracteres para o nome',
            'NOME.max' => ' O nome não pode ter mais de 100 caracteres'
        ];
    }
}
