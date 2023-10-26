<template>
  <v-app-bar app>
    <v-container>
      <v-toolbar-title>
        <v-btn text href="/">Retour au magasin</v-btn>
      </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn v-if="user" text @click="logout">Deconnexion</v-btn>
      <template v-else>
        <v-btn text href="/login">Connexion</v-btn>
        <v-btn text href="/register">Inscription</v-btn>
      </template>
    </v-container>
  </v-app-bar>
</template>

<script>
export default {
  data() {
    return {
      user: false,
    };
  },
  methods: {
    logout() {
      // Logic for logging out
      this.user = null;
    }
  }
};
</script>

<style scoped>
/* Styles personnalisés ici */
</style>
