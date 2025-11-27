<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function buscar($cep)
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return response()->json(['erro' => 'CEP inválido'], 400);
        }

        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

        if ($response->failed() || isset($response['erro'])) {
            return response()->json(['erro' => 'CEP não encontrado'], 404);
        }

        return $response->json();
    }
}
