<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            // Pega o ID do usuário da rota (ex: /users/1)
        $userId = $this->route('user');

        return [
            // --- Dados Básicos ---
            'role_id' => 'required|integer|exists:roles,id', // Garante que a role existe
            'name'    => 'required|string|max:255',
            'apelido' => 'nullable|string|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'documento' => [
                'required',
                'string',
                // Se você salva com pontuação no banco, use assim. 
                // Se salva apenas números, precisa tratar antes (veja a dica abaixo).
                Rule::unique('users')->ignore($userId), 
            ],

            'telefone' => 'required|string', // Pode adicionar regex se quiser forçar formato

            // --- Endereço (Campos planos/flat) ---
            'cep'         => 'nullable|string',
            'numero'      => 'nullable|string',
            'complemento' => 'nullable|string',

            // --- Endereço (Objeto Aninhado) ---
            // Se você precisa validar os dados que vêm dentro do objeto "endereco":
            'endereco.logradouro' => 'nullable|string',
            'endereco.bairro'     => 'nullable|string',
            'endereco.cidade.nome'=> 'nullable|string',
            
            // CUIDADO AQUI: Foi onde deu o erro da tabela 'pais' antes.
            // Se 'endereco.cidade.estado.uf' for apenas informativo, deixe 'string'.
            // Só use 'exists' se tiver certeza do nome da tabela de estados.
            'endereco.cidade.estado.uf' => 'nullable|string|size:2', 
        ];
    }

    protected function prepareForValidation()
    {
        // Remove tudo que não é número do documento e cep antes de validar
        $this->merge([
            'documento' => preg_replace('/[^0-9]/', '', $this->documento),
            'cep'       => preg_replace('/[^0-9]/', '', $this->cep),
            'telefone'  => preg_replace('/[^0-9]/', '', $this->telefone),
        ]);
    }
}
