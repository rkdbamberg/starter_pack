<template>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8">
          <EditProfileForm :user="user" />
        </div>
        <div class="col-md-4">
          <UserCard :user="user" />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import EditProfileForm from './UserProfile/EditProfileForm.vue'
  import UserCard from './UserProfile/UserCard.vue'
  import axios from 'axios'

  export default {
    components: {
      EditProfileForm,
      UserCard
    },
    data () {
      return {
        user: null
      }
    },
    mounted(){
      const token = localStorage.getItem('access_token');
      axios.get('/api/user', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
      .then(response => {
        this.user = response.data;
      })
      .catch(error => {
        if (error.response && error.response.status === 401) {
          this.$router.push('/login');
        }
      });
    }
  }

</script>
<style>

</style>
