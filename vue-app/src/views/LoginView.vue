<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar color="primary" dark flat>
            <v-toolbar-title>Se connecter</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="login">
              <v-text-field
                label="E-mail"
                name="email"
                v-model="username"
                prepend-icon="mdi-email"
                type="email"
                required
              ></v-text-field>
              <v-text-field
                label="Mot de passe"
                name="password"
                v-model="password"
                prepend-icon="mdi-lock"
                type="password"
                required
              ></v-text-field>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" block @click="login">Se connecter</v-btn>
          </v-card-actions>
          <v-card-actions class="justify-center">
            <v-btn text small @click="$router.push('/register')">Vous n'avez pas de compte ? Inscription</v-btn>
          </v-card-actions>
          <v-alert v-if="error" type="error" dense outlined>
            {{ errorMessage }}
          </v-alert>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
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