<template>
  <nav class="navbar navbar-expand-md navbar-light fixed-top bg-white border-bottom border-dark border-2">
    <div class="container-fluid mx-5">
      <a class="navbar-brand" href="/">Shop</a>
      <div class="mx-5 d-flex justify-content-end align-items-center ">
        <div v-if="user">
          <p class="my-2 mx-3">{{ user.email }}</p>
          <a href="/logout">
            <button type="button" class="btn btn-primary me-2">Deconnexion</button>
          </a>
        </div>
        <div v-else>
          <a href="/login">
            <button type="button" class="btn btn-primary me-2">Connexion</button>
          </a>
          <a href="/register">
            <button type="button" class="btn btn-warning">
              Inscription
            </button>
          </a>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  data() {
    return {
      user: null
    };
  },
  methods: {
  }
};
</script>

<style scoped>
</style>
