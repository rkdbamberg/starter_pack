<template>
  <div class="login-container">
    <h2>Login</h2>
    <form @submit.prevent="handleLogin">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="email" required>
      </div>
      <div class="form-group">
        <label for="password">Senha:</label>
        <input type="password" id="password" v-model="password" required>
      </div>
      <button type="submit">Entrar</button>
      <p v-if="error" class="error-message">{{ error }}</p>
    </form>
    <p>Não tem uma conta? <router-link to="/register">Registre-se</router-link></p>
  </div>
</template>

<script>
import axios from 'axios'; // Importar Axios, se não estiver global

export default {
  data() {
    return {
      email: '',
      password: '',
      error: null
    };
  },
  methods: {
    async handleLogin() {
      this.error = null; // Limpa erros anteriores
      try {
        // Requisição para a API de login do Laravel
        await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });

        // Se o login for bem-sucedido, redireciona para o dashboard
        this.$router.push('/admin/overview'); // Assumindo que você tem uma rota '/dashboard'
      } catch (err) {
        this.error = 'Falha no login. Verifique suas credenciais.';
        console.error('Erro de login:', err);
        // Capturar e exibir mensagens de erro específicas do Laravel, se houver
        if (err.response && err.response.data && err.response.data.errors) {
          this.error = Object.values(err.response.data.errors).flat().join(' ');
        }
      }
    }
  }
};
</script>

<style scoped>
/* Estilos básicos para o formulário de login */
.login-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}
.form-group {
  margin-bottom: 15px;
  text-align: left;
}
label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box; /* Garante que padding não aumente a largura total */
}
button {
  background-color: #007bff;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}
button:hover {
  background-color: #0056b3;
}
.error-message {
  color: red;
  margin-top: 10px;
}
</style>