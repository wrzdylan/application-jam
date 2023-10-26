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
                :rules="[v => !!v || 'Password is required']"
                required
              ></v-text-field>
              <v-checkbox
                v-model="formData.agreeTerms"
                label="J'accepte les termes."
                :rules="[v => !!v || 'You must agree to continue']"
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
export default {
  data() {
    return {
      formData: {
        email: '',
        plainPassword: '',
        agreeTerms: false,
        emailRules: [ 
          v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail doit être valide'
        ]
      }
    };
  },
  methods: {
    submitForm() {
      if (this.$refs.form.validate()) {
        // Your form submission logic here
      }
    }
  }
}
</script>
