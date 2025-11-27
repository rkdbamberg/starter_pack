<?php

namespace App\Services;

use App\Models\Endereco;
use App\Models\Cidade;
use App\Models\Estado;
use App\Models\Pais;
use Illuminate\Support\Facades\Http;

class AddressService
{
    /**
     * Busca um endereço pelo CEP.
     * 1. Procura no banco local.
     * 2. Se não achar, busca na API ViaCEP e cadastra toda a hierarquia.
     */
    public function buscarOuCriarPorCep(string $cep)
    {
        // 1. Limpa o CEP (deixa só números)
        $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

        if (strlen($cepLimpo) !== 8) {
            return null; // CEP inválido
        }

        // 2. Tenta achar no banco primeiro (Performance 🚀)
        $enderecoExistente = Endereco::where('cep', $cepLimpo)->first();
        if ($enderecoExistente) {
            return $enderecoExistente;
        }

        // 3. Consulta API Externa (ViaCEP)
        $response = Http::get("https://viacep.com.br/ws/{$cepLimpo}/json/");

        if ($response->failed()) {
            return null; // Erro de conexão
        }

        $data = $response->json();

        if (isset($data['erro'])) {
            return null; // CEP não existe na API
        }

        // 4. Inicia a criação em cascata (Hierarquia)
        
        // A. PAÍS (Assumimos Brasil)
        $pais = Pais::firstOrCreate(
            ['sigla' => 'BR'],
            ['nome' => 'Brasil']
        );

        // B. ESTADO
        // Dica: O ViaCEP só retorna a UF (ex: SP). Se quiser o nome completo, precisaria de um array de-para.
        // Por simplicidade, vou salvar o nome igual a UF se não tivermos um mapa.
        $nomeEstado = $this->getNomeEstado($data['uf']); 
        
        $estado = Estado::firstOrCreate(
            ['uf' => $data['uf'], 'pais_id' => $pais->id],
            ['nome' => $nomeEstado]
        );

        // C. CIDADE
        $cidade = Cidade::firstOrCreate(
            ['nome' => $data['localidade'], 'estado_id' => $estado->id]
        );

        // D. ENDEREÇO (Finalmente!)
        $endereco = Endereco::create([
            'cep'        => $cepLimpo,
            'logradouro' => $data['logradouro'] ?? 'Logradouro Desconhecido', // Fallback
            'bairro'     => $data['bairro'] ?? 'Centro',
            'cidade_id'  => $cidade->id
        ]);

        return $endereco;
    }

    // Helper para converter UF em Nome (Opcional, mas deixa o banco bonito)
    private function getNomeEstado($uf)
    {
        $estados = [
            'AC'=>'Acre', 'AL'=>'Alagoas', 'AP'=>'Amapá', 'AM'=>'Amazonas', 'BA'=>'Bahia', 'CE'=>'Ceará',
            'DF'=>'Distrito Federal', 'ES'=>'Espírito Santo', 'GO'=>'Goiás', 'MA'=>'Maranhão', 'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul', 'MG'=>'Minas Gerais', 'PA'=>'Pará', 'PB'=>'Paraíba', 'PR'=>'Paraná',
            'PE'=>'Pernambuco', 'PI'=>'Piauí', 'RJ'=>'Rio de Janeiro', 'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul', 'RO'=>'Rondônia', 'RR'=>'Roraima', 'SC'=>'Santa Catarina',
            'SP'=>'São Paulo', 'SE'=>'Sergipe', 'TO'=>'Tocantins'
        ];

        return $estados[$uf] ?? $uf;
    }
}