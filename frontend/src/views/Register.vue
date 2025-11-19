<!-- src/views/Register.vue -->
<template>
  <div class="register-container">
    <h2>Crie sua conta</h2>

    <!-- Exibe mensagem de erro se houver -->
    <div v-if="errorMessage" class="error-alert">
      {{ errorMessage }}
    </div>
    
    <form @submit.prevent="handleRegister">
      <div class="form-group">
        <label>Nome:</label>
        <input type="text" v-model="name" required placeholder="Seu nome completo" />
      </div>

      <div class="form-group">
        <label>Email:</label>
        <input type="email" v-model="email" required placeholder="seu@email.com" />
      </div>

      <div class="form-group">
        <label>Senha:</label>
        <input type="password" v-model="password" required placeholder="Sua senha" />
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
      </button>
    </form>

    <p>
      Já tem uma conta? 
      <router-link to="/login">Faça Login aqui</router-link>
    </p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      errorMessage: null,
      loading: false
    }
  },
  methods: {
    async handleRegister() {
      
      this.loading = true;
      this.errorMessage = null;

      try {
        const response = await axios.post('http://127.0.0.1:8002/api/register', {
          name: this.name,
          email: this.email,
          password: this.password
        });

        console.log('Registro bem-sucedido:', response.data);

        const token = response.data.access_token;
        localStorage.setItem('authToken', token);

        localStorage.setItem('user', JSON.stringify(response.data.user));

        this.$router.push('/admin/overview');

      } catch (error) {
        console.error('Erro no registro:', error);

        if(error.response && error.response.data){
          this.errorMessage = error.response.data.message || 'Erro ao cadastrar.';
        } else {
          this.errorMessage = 'Erro de conexão com o servidor.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.register-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  text-align: center;
}
.form-group {
  margin-bottom: 15px;
  text-align: left;
}
input {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  box-sizing: border-box;
}
button {
  width: 100%;
  padding: 10px;
  background-color: #42b983;
  color: white;
  border: none;
  cursor: pointer;
  margin-top: 10px;
}
button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}
.error-alert {
  background-color: #f8d7da;
  color: #721c24;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 4px;
}

</style>