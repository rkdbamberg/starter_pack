<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Aqui você retornaria dados reais (vendas, estatísticas, etc)
        return response()->json([
            'message' => 'Bem-vindo ao Dashboard!',
            'user' => auth()->user(), // Dados do usuário logado
            'stats' => [
                'total_vendas' => 150,
                'novos_clientes' => 12
            ]
        ]);
    }
}