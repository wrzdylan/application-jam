<template>
    <div>
      <div v-if="cart.length > 0" class="row">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12">
            <div v-for="product in cart" :key="product.id" class="row mb-4 d-flex justify-content-between align-items-center">
              <img :src="getProductImage(product)" class="img-fluid rounded-3">
              <span class="quantity fw-bold fs-4">{{ product.quantity }}</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
              <h5 class="text-uppercase">Total</h5>
              <h5>€ {{ formatCurrency(total) }}</h5>
            </div>
            <div v-if="!user">
            </div>
            <div v-else>
              <a class="m-5 btn btn-dark" :href="checkoutPath" role="button">VALIDER LE PANIER</a>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="row">
        <p>Le panier est vide.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        cart: [], 
        user: null, 
        total: 0 
      };
    },
    computed: {
      checkoutPath() {
        return '/path/to/checkout'; 
      }
    },
    methods: {
      getProductImage(product) {
        return `/uploads/${product.image}`;
      },
      formatCurrency(value) {
        return new Intl.NumberFormat('fr-FR', {
          style: 'currency',
          currency: 'EUR'
        }).format(value);
      }
    },
    watch: {
      cart: {
        handler() {
          this.total = this.cart.reduce((acc, product) => acc + product.price/100 * product.quantity, 0);
        },
        deep: true
      }
    },
    created() {
    }
  }
  </script>
  