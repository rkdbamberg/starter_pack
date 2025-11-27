<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // MUDANÇA AQUI:
            // Renomeamos para 'documento' e aumentamos o tamanho.
            // 14 digitos (apenas números) ou 18 (com pontos/traços).
            $table->string('documento', 18)->unique()->nullable()->comment('CPF ou CNPJ');

            $table->string('apelido')->nullable();
            $table->string('telefone', 20)->nullable();

            // Chaves Estrangeiras
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->foreignId('endereco_id')->nullable()->constrained('enderecos');

            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
