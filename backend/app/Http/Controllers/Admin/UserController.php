<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Services\AddressService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::with('role')->latest()->get(); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'nullable|exists:roles,id' // Valida se a role existe
        ]);

        // Hash da senha é obrigatório
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::with('role')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id, AddressService $addressService)
    {
        $user = User::findOrFail($id);
        $data = $request->validated(); // Pega os dados validados

        // --- LÓGICA DE ENDEREÇO ---
        // Se o usuário mandou um CEP, vamos resolver o ID do endereço
        if (!empty($request->cep)) {
            
            $endereco = $addressService->buscarOuCriarPorCep($request->cep);

            if ($endereco) {
                // Vincula o ID do endereço encontrado/criado ao usuário
                $data['endereco_id'] = $endereco->id;
            } else {
                // Se retornou null, o CEP é inválido
                return response()->json(['message' => 'CEP não encontrado ou inválido.'], 422);
            }
        }
        // --------------------------

        // Se tiver senha, faz o hash
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Limpa o documento (CPF/CNPJ) antes de salvar, se quiser salvar só números
        if (isset($data['documento'])) {
            $data['documento'] = preg_replace('/[^0-9]/', '', $data['documento']);
        }

        // Atualiza o usuário
        $user->update($data);

        // Retorna o usuário com os dados do endereço carregados
        return response()->json(
            $user->load(['role', 'endereco.cidade.estado'])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
