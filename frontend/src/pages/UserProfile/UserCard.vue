<template>
  <card class="card-user">
    <img slot="image" src="https://ununsplash.imgix.net/photo-1431578500526-4d9613015464?fit=crop&fm=jpg&h=300&q=75&w=400" alt="..."/>
    <div class="author">
      <a href="#">
        <img class="avatar border-gray" src="img/faces/face-3.jpg" alt="..."/>

        <h4 class="title">user.name<br />
          <small>michael24</small>
        </h4>
      </a>
    </div>
    <p class="description text-center"> "Lamborghini Mercy <br>
      Your chick she so thirsty <br>
      I'm in that two seat Lambo"
    </p>
    <div slot="footer" class="text-center d-flex justify-content-center">
      <button href="#" class="btn btn-simple"><i class="fa fa-facebook-square"></i></button>
      <button href="#" class="btn btn-simple"><i class="fa fa-twitter"></i></button>
      <button href="#" class="btn btn-simple"><i class="fa fa-google-plus-square"></i></button>
    </div>
  </card>
</template>
<script>
  import Card from 'src/components/Cards/Card.vue'
  export default {
    components: {
      Card
    },
    props: ['user']
  }

</script>
<style>

</style>
