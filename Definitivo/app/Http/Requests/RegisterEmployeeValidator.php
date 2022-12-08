<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterEmployeeValidator extends FormRequest
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
            'NAME' => 'required|max:50|min:4',
            'CPF' => 'required',
            'EMAIL'=> 'required|max:60|min:8|email',
            'ID_ROLE' => 'required'
        ];
    }
}
