<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email', // Email deve ser único na tabela toda
            'password'   => 'required|string|min:6|confirmed', // 'confirmed' exige um campo password_confirmation no front
            'role_id'    => 'required|integer|exists:roles,id',
            
            // Dados Pessoais (Podem ser opcionais ou obrigatórios dependendo da sua regra)
            'cpf'        => 'nullable|string|max:14|unique:users,cpf',
            'apelido'    => 'nullable|string|max:20',
            'telefone'   => 'nullable|string|max:20',
            
            // Endereço
            'cep'        => 'nullable|string|max:9',
            'logradouro' => 'nullable|string|max:255',
            'numero'     => 'nullable|string|max:10',
            'cidade'     => 'nullable|string|max:10',
            'estado'     => 'nullable|string|max:2',
            'pais'       => 'nullable|string|max:10',
            'bairro'     => 'nullable|string|max:50',
            'complemento'=> 'nullable|string|max:50',
        ];
    }
}
