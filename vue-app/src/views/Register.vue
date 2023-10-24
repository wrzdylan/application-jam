<template>
    <div>
      <h1>Inscription</h1>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" v-model="formData.email" id="email" class="form-control">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" v-model="formData.plainPassword" id="password" class="form-control">
        </div>
        <div class="form-check">
          <input type="checkbox" v-model="formData.agreeTerms" id="agreeTerms" class="form-check-input">
          <label for="agreeTerms" class="form-check-label">J'accepte les termes.</label>
        </div>
        <button type="submit" class="btn btn-primary">Valider</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        formData: {
          email: '',
          plainPassword: '',
          agreeTerms: false
        }
      };
    },
    methods: {
      async submitForm() {
        try {

          const response = await axios.post('/register-endpoint', this.formData);
          if(response.status === 200) {
          }
        } catch(error) {
        }
      }
    }
  };
  </script>
  