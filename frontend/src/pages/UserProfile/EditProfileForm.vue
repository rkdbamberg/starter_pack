<template>
  <!-- Usamos v-if no user para garantir que a prop chegou -->
  <card v-if="user">
    <h4 slot="header" class="card-title">Editar usuário</h4>
    <form @submit.prevent="updateProfile">
      <div class="row">
        <div class="col-md-5">
          <!-- IMPORTANTE: v-model apontando para FORM, não user -->
          <base-select
            label="Perfil de Usuário"
            v-model="form.role_id"
            default-option="Selecione o perfil ..."
            :options="roles">
          </base-select>
        </div>
        <div class="col-md-3">
          <base-input type="text"
                    ref="docInput"
                    label="CPF / CNPJ"
                    :mask="['###.###.###-##', '##.###.###/####-##']"
                    placeholder="Somente números"
                    v-model="form.documento">
          </base-input>
        </div>
        <div class="col-md-4">
          <base-input type="email"
                    label="Email"
                    placeholder="Email"
                    v-model="form.email">
          </base-input>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8">
          <base-input type="text"
                    label="Nome Completo / Razão Social"
                    placeholder="Nome Completo"
                    v-model="form.name">
          </base-input>
        </div>
        <div class="col-md-4">
          <base-input type="text"
                    label="Nome Social / Nome Fantasia"
                    placeholder="Nome Social"
                    v-model="form.apelido">
          </base-input>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <!-- CORREÇÃO PRINCIPAL: @blur.native e v-model="form.cep" -->
          <base-input 
            type="text" 
            label="CEP"
            placeholder="00.000-000"
            mask="##.###-###"
            v-model="form.cep"
            @blur="buscarCep"
          >
          </base-input>
          
          <!-- Mensagens de status do CEP -->
          <small v-if="isLoadingCep" class="text-info">Buscando endereço...</small>
          <small v-if="cepError" class="text-danger">{{ cepError }}</small>
        </div>

        <div class="col-md-4">
          <base-input type="text"
                    label="Telefone / Celular"
                    placeholder="Telefone"
                    :mask="['(##) ####-####', '(##) #####-####']"
                    v-model="form.telefone">
          </base-input>
        </div>
      </div>

      <!-- CAMPOS DE ENDEREÇO (Preenchidos pelo CEP) -->
      <!-- Adicionei 'disabled' para o usuário não digitar errado, já que vem do CEP -->
      <div class="row">
        <div class="col-md-12">
          <base-input type="text"
                    label="Logradouro"
                    placeholder="Endereço"
                    v-model="form.endereco.logradouro"
                    disabled>
          </base-input>
        </div>
      </div>

      <div class="row">
        <div class="col-md-5">
          <base-input type="text"
                    label="Cidade"
                    placeholder="Cidade"
                    v-model="form.endereco.cidade.nome"
                    disabled>
          </base-input>
        </div>
        <div class="col-md-3">
          <base-input type="text"
                    label="Estado"
                    placeholder="UF"
                    v-model="form.endereco.cidade.estado.uf"
                    disabled>
          </base-input>
        </div>
         <div class="col-md-4">
          <base-input type="text"
                    label="Bairro"
                    placeholder="Bairro"
                    v-model="form.endereco.bairro"
                    disabled>
          </base-input>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <!-- Número e Complemento continuam editáveis -->
          <base-input type="text"
                    label="Número"
                    placeholder="Número"
                    v-model="form.numero">
          </base-input>
        </div>
        <div class="col-md-8">
          <base-input type="text"
                    label="Complemento"
                    placeholder="Complemento"
                    v-model="form.complemento">
          </base-input>
        </div>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-info btn-fill float-right" @click.prevent="updateProfile" :disabled="loading">
          {{ loading ? 'Salvando  Alterações...' : 'Salvar Alterações' }}
        </button>
      </div>
      <div class="clearfix"></div>
    </form>
  </card>
</template>

<script>
  import Card from 'src/components/Cards/Card.vue'
  import BaseSelect from 'src/components/Inputs/BaseSelect.vue' 
  import axios from 'axios' 
  
  export default {
  // 2. REGISTRAR OS COMPONENTES
  components: {
    Card,
    BaseSelect
  },
  props: {
    user: {
      type: Object,
      default: () => null
    }
  },
  data () {
    return {
      loading: false,
      isLoadingCep: false,
      cepError: '',
      form: {
        role_id: '',
        documento: '',
        email: '',
        name: '',
        apelido: '',
        telefone: '',
        cep: '',
        numero: '',
        complemento: '',
        // Inicializar a estrutura para não dar erro de "undefined" no v-model
        endereco: {
          logradouro: '',
          bairro: '',
          cidade: {
            nome: '',
            estado: {
              uf: ''
            }
          }
        }
      },
      roles: [
        { value: '1', text: 'Administrador' },
        { value: '2', text: 'Interno' },
        { value: '3', text: 'Cliente' }
      ]
    }
  },
  watch: {
    user: {
      handler(newUser) {
        if (!newUser) return;

        this.form.role_id = newUser.role_id || '';
        this.form.documento = newUser.documento || '';
        this.form.email = newUser.email || '';
        this.form.name = newUser.name || '';
        this.form.apelido = newUser.apelido || '';
        this.form.telefone = newUser.telefone || '';
        this.form.cep = newUser.cep || ''; // Importante: Se já tiver CEP, preenche
        this.form.numero = newUser.numero || '';
        this.form.complemento = newUser.complemento || '';
          
        // Se o usuário vindo do banco já tiver endereço
        if (newUser.endereco) {
          // Precisamos garantir que a estrutura existe para não quebrar o HTML
          this.form.endereco = {
            logradouro: newUser.endereco.logradouro || '',
            bairro: newUser.endereco.bairro || '',
            cidade: {
              nome: newUser.endereco.cidade ? newUser.endereco.cidade.nome : '',
              estado: {
                uf: newUser.endereco.cidade && newUser.endereco.cidade.estado ? newUser.endereco.cidade.estado.uf : ''
              }
            }
          }
        }
      },
      immediate: true
    }
  },
  methods: {

    validateForm() {
      if (!this.form.role_id) return "Selecione o perfil";
      if (!this.form.documento) return "Informe o CPF/CNPJ";
      if (!this.form.email) return "Informe o email";
      if (!this.form.name) return "Informe o nome";
      if (!this.form.telefone) return "Informe o telefone";
      if (!this.form.cep) return "Informe o CEP";
      return null;
    },
      
    buscarCep () {

      // Remove caracteres não numéricos
      const cep = this.form.cep.replace(/\D/g, '');

      if (cep === "") return;
      
      // Validação simples de tamanho
      if (cep.length !== 8) {
        this.cepError = "CEP deve ter 8 dígitos";
        return;
      }

      this.isLoadingCep = true;
      this.cepError = '';

      // Chamada à API
      axios.get(`/api/cep/${cep}`)
        .then(response => {
            this.form.endereco = {
              logradouro: response.data.logradouro,
              bairro: response.data.bairro,
              cidade: {
                nome: response.data.localidade,
                estado: { uf: response.data.uf }
              }
            };
        })
        .catch(() => {
            this.cepError = 'Erro ao buscar CEP';
        }).finally(() => {
          this.isLoadingCep = false;
        });
    },

    limparEndereco() {
        this.form.endereco.logradouro = '';
        this.form.endereco.bairro = '';
        this.form.endereco.cidade.nome = '';
        this.form.endereco.cidade.estado.uf = '';
    },

    async updateProfile() {
        this.loading = true;
        try {
            await axios.put(`/api/admin/users/${this.user.id}`, this.form);

            this.$notify({ type: 'success', message: 'Salvo com sucesso!' });

        } catch (error) {
            if (error.response && error.response.status === 401) {
                this.$notify({ type: 'danger', message: 'Sessão expirada. Faça login novamente.' });
                // Redirecionar para login aqui se necessário
            } else {
                this.$notify({ type: 'danger', message: 'Erro ao atualizar.' });
            }
            console.error(error);
        } finally {
            this.loading = false;
        }
    }
  }
}
</script>