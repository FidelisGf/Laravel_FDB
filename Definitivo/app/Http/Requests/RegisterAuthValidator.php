<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAuthValidator extends FormRequest
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
            'PASSWORD' => 'required|max:50|min:6',
            'EMAIL'=> 'required|max:60|min:8|email',
            'ID_ROLE' => 'required'
        ];
    }
}
