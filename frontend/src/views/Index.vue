<template>
  <div class="index-container">
    <h1>Bem-vindo ao Projeto</h1>
    <p>Esta é a página inicial (Landing Page).</p>
    
    <!-- Link para a rota de Login -->
    <router-link to="/login" class="btn btn-login">
      Acessar o Sistema (Login)
    </router-link>

    <!-- Link para Registro -->
    <router-link to="/register" class="btn btn-register">
      Criar Conta
    </router-link>
  </div>
</template>

<script>
export default {
  name: 'IndexPage'
}
</script>

<style scoped>
/* Estilo básico apenas para visualizar */
.index-container {
  text-align: center;
  margin-top: 50px;
}
.actions {
  margin-top: 20px;
  display: flex;
  gap: 15px; /* Espaço entre os botões */
  justify-content: center;
}
.btn {
  padding: 10px 20px;
  text-decoration: none;
  border-radius: 4px;
  font-weight: bold;
}
.btn-login {
  background-color: #42b983; /* Verde Vue */
  color: white;
}
.btn-register {
  background-color: #35495e; /* Azul Escuro Vue */
  color: white;
}
</style>