<template>
  <div class="card">
    <div class="card-header">
      <h3>Meu Perfil</h3>
    </div>
    
    <div class="card-body">
      <div v-if="loading">
        Carregando dados...
      </div>

      <form v-else @submit.prevent="updateProfile">
        <!-- Nome -->
        <div class="form-group mb-3">
          <label>Nome</label>
          <input type="text" v-model="user.name" class="form-control">
        </div>

        <!-- Email (Geralmente Readonly) -->
        <div class="form-group mb-3">
          <label>Email</label>
          <input type="email" v-model="user.email" class="form-control" readonly>
        </div>

        <!-- Grupo/Role (Apenas Leitura) -->
        <div class="form-group mb-3">
          <label>Grupo de Acesso</label>
          <input 
            type="text" 
            :value="user.role ? user.role.name : 'Sem grupo'" 
            class="form-control" 
            disabled
          >
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'; // Certifique-se que o axios está importado/configurado

export default {
  name: 'UserProfile',
  data() {
    return {
      loading: true,
      user: {
        name: '',
        email: '',
        role: null
      }
    };
  },
  mounted() {
    this.fetchUser();
  },
  methods: {
    fetchUser() {
      // O Axios precisa estar enviando o Token no Header (Authorization: Bearer ...)
      // Geralmente isso é configurado globalmente, mas aqui é a chamada direta:
      axios.get('/api/user')
        .then(response => {
          this.user = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error("Erro ao carregar usuário:", error);
          this.loading = false;
          
          // Se der erro 401 (não autorizado), redireciona pro login
          if (error.response && error.response.status === 401) {
            this.$router.push('/login');
          }
        });
    },
    
    updateProfile() {
      // Exemplo simples de como salvar a alteração do nome
      axios.put('/api/user/profile-information', { // O Laravel Fortify usa essa rota, ou você cria a sua
          name: this.user.name,
          email: this.user.email
      })
      .then(() => {
          alert('Perfil atualizado com sucesso!');
      })
      .catch(error => {
          alert('Erro ao atualizar.');
      });
    }
  }
};
</script>

<style scoped>
.card {
  margin: 20px;
  padding: 20px;
}
</style>