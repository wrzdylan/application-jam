<template>
  <v-container>
    <v-row>
      <v-col cols="12" md="6">
        <v-card>
          <v-card-title>Inscription</v-card-title>
          <v-card-text>
            <v-form ref="form" @submit.prevent="submitForm">
              <v-text-field
                v-model="formData.email"
                label="Email"
                type="email"
                :rules="emailRules"
                required
              ></v-text-field>
              <v-text-field
                v-model="formData.plainPassword"
                label="Password"
                type="password"
                :rules="[v => !!v || 'Le mot de passe est requis']"
                required
              ></v-text-field>
              <v-checkbox
                v-model="formData.agreeTerms"
                label="J'accepte les termes."
                :rules="[v => !!v || 'Vous devez accepter les termes']"
                required
              ></v-checkbox>
              <v-btn
                type="submit"
                color="primary"
                :disabled="!$refs.form?.validate()"
              >
                Valider
              </v-btn>
            </v-form>
          </v-card-text>
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
          const response = await axios.post('http://localhost:8000/api/users', {
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