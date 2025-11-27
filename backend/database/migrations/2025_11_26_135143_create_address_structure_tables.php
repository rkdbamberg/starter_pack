<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressStructureTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabela PAIS
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('sigla', 3)->nullable(); // BR, US
            $table->timestamps();
        });

        // 2. Tabela ESTADOS
        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('uf', 2);
            $table->foreignId('pais_id'); // Sem constrained ainda
            $table->timestamps();
        });

        // 3. Tabela CIDADES
        Schema::create('cidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('estado_id'); // Sem constrained ainda
            $table->timestamps();
        });

        // 4. Tabela ENDEREÇOS (Base de CEP)
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('cep', 10)->unique()->index();
            $table->string('logradouro');
            $table->string('bairro');
            $table->foreignId('cidade_id'); // Sem constrained ainda
            $table->timestamps();
        });

        // 5. AGORA AMARRAMOS TUDO (Foreign Keys)
        Schema::table('estados', function (Blueprint $table) {
            $table->foreign('pais_id')->references('id')->on('paises');
        });

        Schema::table('cidades', function (Blueprint $table) {
            $table->foreign('estado_id')->references('id')->on('estados');
        });

        Schema::table('enderecos', function (Blueprint $table) {
            $table->foreign('cidade_id')->references('id')->on('cidades');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address_structure_tables');
    }
}
