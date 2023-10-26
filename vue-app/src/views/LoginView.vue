<template>
    <div class="row col-6">
      <div v-if="error">{{ errorMessage }}</div>
      
      <h1 class="my-5">Login</h1>
      <form @submit.prevent="login">
        <!-- Email input -->
        <div class="form-outline mb-4">
          <input type="text" class="form-control" v-model="username">
          <label class="form-label" for="username">E-mail</label>
        </div>
        
        <!-- Password input -->
        <div class="form-outline mb-4">
          <input type="password" v-model="password" class="form-control">
          <label class="form-label" for="password">Mot de passe</label>
        </div>
  
        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block mb-4">Se connecter</button>
  
        <!-- Register buttons -->
        <div class="text-center">
          <p>Vous n'avez pas de compte ? <router-link to="/register">Inscription</router-link></p>
        </div>
      </form>
    </div>
  </template>
  
<script>
import axios from 'axios';

export default {
  data() {
    return {
        username: '',
        password: '',
        errorMessage: null,
        error: false
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post('http://localhost:8080/api/login_check', {
          email: this.username,
          password: this.password
        });

        localStorage.setItem('token', response.data.token);
        const now = new Date();
        const expirationTime = new Date(now.getTime() + 60 * 60 * 1000);  // 1 heure plus tard
        localStorage.setItem('tokenExpiration', expirationTime);

        this.$router.push({ name: 'home' });
      } catch (error) {
        this.error = true;
        this.errorMessage = error.response ? error.response.data.message : error.message;
      }
    }
  }
}
</script>

<style scoped>
</style>