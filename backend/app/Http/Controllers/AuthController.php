<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
    // Bloqueia tudo, EXCETO login e register
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    // REGISTRO
    public function register(Request $request)
    {
        // 1. Validação
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', // Confirmação de senha é opcional no front
        ]);

        // 2. Criar Usuário
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Criar Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Retornar resposta
        return response()->json([
            'message' => 'Usuário cadastrado com sucesso!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        // Apagar tokens antigos se quiser login único, ou apenas criar novo
        // $user->tokens()->delete(); 

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        // Remove o token que foi usado na requisição
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logout realizado com sucesso']);
    }
}