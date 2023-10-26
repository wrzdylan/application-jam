<template>
  <v-container class="fill-height" fluid>
    <v-row align="center" justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card class="elevation-12">
          <v-toolbar color="primary" dark flat>
            <v-toolbar-title>Inscription</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form ref="form" @submit.prevent="submitForm">
              <v-text-field
                v-model="formData.email"
                label="E-mail"
                type="email"
                :rules="formData.emailRules"
                prepend-icon="mdi-email"
                required
              ></v-text-field>
              <v-text-field
                v-model="formData.plainPassword"
                label="Mot de passe"
                type="password"
                :rules="[v => !!v || 'Le mot de passe est requis']"
                prepend-icon="mdi-lock"
                required
              ></v-text-field>
              <v-checkbox
                v-model="formData.agreeTerms"
                label="J'accepte les termes."
                :rules="[v => !!v || 'Vous devez accepter les termes']"
                required
              ></v-checkbox>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" block @click="submitForm">Valider</v-btn>
          </v-card-actions>
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
      formData: {
        email: '',
        plainPassword: '',
        agreeTerms: false,
        emailRules: [
          v => !!v && /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail doit être valide'
        ]
      }
    };
  },
  methods: {
    async submitForm() {
      if (this.$refs.form.validate()) {
        try {
          const response = await axios.post('http://localhost:8080/api/users', {
            email: this.formData.email,
            // roles: ['string'],  // Assurez-vous de remplacer 'string' par la valeur appropriée
            password: this.formData.plainPassword,
            // orders: ['path/실례.html']  // Assurez-vous de remplacer 'path/실례.html' par le chemin approprié
          });

          // Logique à exécuter après une réponse réussie
          console.log('Réponse réussie:', response.data);
        } catch (error) {
          // Gestion des erreurs lors de l'envoi de la requête
          console.error('Erreur:', error.response ? error.response.data : error.message);
        }
      }
    }
  }
}
</script>

<style scoped>
</style>
