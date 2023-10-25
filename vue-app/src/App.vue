<template>
  <v-app>
    <HeaderLayout/>

    <v-main>
      <router-view/>
    </v-main>
  </v-app>
</template>

<script>
import HeaderLayout from './components/HeaderLayout.vue'

export default {
  name: 'App',

  components: {
    HeaderLayout
  },

  data: () => ({
    //
  }),
}
</script>
