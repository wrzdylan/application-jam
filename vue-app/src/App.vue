<template>
  <div>
    <header-component></header-component>

    <main class="container">
      <div v-for="(messages, label) in flashes" :key="label">
        <div v-for="message in messages" :key="message" :class="'alert alert-' + label" role="alert">
          {{ message }}
        </div>
      </div>

      <div class="row">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script>
import HeaderComponent from './Header.vue'; 

export default {
  components: {
    HeaderComponent
  },
  data() {
    return {
      flashes: {}
    };
  }
};
</script>

<style>
</style>
