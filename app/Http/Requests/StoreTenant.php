<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenant extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'subdomain' => ['required', 'string', 'min:2', 'max:255', 'alpha_num', 'unique:tenants,subdomain'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Compo obrigatório',
            'min' => 'Digite no mínimo :min caracteres',
            'max' => 'Digite no máximo :max caracteres',
            'subdomain.alpha_num' => 'Use somente letras e números',
            'subdomain.unique' => 'Sub domínio já em uso'
        ];
    }
}
