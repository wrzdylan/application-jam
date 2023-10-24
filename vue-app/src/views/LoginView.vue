<template>
    <div class="row col-6">
      <div v-if="error">{{ errorMessage }}</div>
      
      <h1 class="my-5">Login</h1>
      <form @submit.prevent="login">
        <!-- Email input -->
        <div class="form-outline mb-4">
          <input type="text" class="form-control" v-model="username">
          <label class="form-label" for="username">Email address</label>
        </div>
        
        <!-- Password input -->
        <div class="form-outline mb-4">
          <input type="password" v-model="password" class="form-control">
          <label class="form-label" for="password">Password</label>
        </div>
  
        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block mb-4">Sign in</button>
  
        <!-- Register buttons -->
        <div class="text-center">
          <p>Not a member? <router-link to="/register">Register</router-link></p>
        </div>
      </form>
    </div>
  </template>
  
  <script>
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
      }
    }
  }
  </script>
  
  <style scoped>
  </style>
  