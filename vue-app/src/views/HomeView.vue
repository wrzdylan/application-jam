<template>
    <div class="row mt-4">
      <div class="col-12 mt-4" id="content">
        <div class="row">
          <!-- Filters -->
          <div class="col-4">
            <form @submit.prevent="filterProducts">
              <div class="my-5" id="slider"></div>
  
              <select v-model="selectedFilter">
                <option v-for="filter in filters" :key="filter.value" :value="filter.value">{{ filter.label }}</option>
              </select>
              <br><br>
              
              <div class="form-check" v-for="category in categories" :key="category.id">
                <input class="form-check-input" type="checkbox" v-model="selectedCategories" :value="category.id">
                <label class="form-check-label">{{ category.name }}</label>
              </div>
              
              <br>
              <button class="btn btn-dark" type="submit">FILTRER</button>
            </form>
          </div>
  
          <!-- Products -->
          <div class="col-8">
            <div class="row">
              <div class="m-2 col-3 m-22 card" v-for="product in products" :key="product.id" :data-product-id="product.id">
                <img class="p-4 card-img-top" :src="product.imageUrl">
                <div class="card-body d-flex flex-column justify-content-between">
                  <h5 class="card-title">{{ product.name }}</h5>
                  <p class="card-text">{{ product.price | currency }}</p>
                  <div class="row">
                    <!-- Product action form (e.g. add to cart) would go here -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        products: [], 
        categories: [], 
        filters: [
          { label: 'Prix croissant', value: 'price_asc' },
          { label: 'Prix décroissant', value: 'price_desc' },
          { label: 'Alphabétique', value: 'name_asc' },
          { label: 'Alphabétique Z->A', value: 'name_desc' }
        ],
        selectedFilter: 'price_asc',
        selectedCategories: []
      };
    },
    methods: {
      filterProducts() {
      }
    },
    computed: {
      currency(value) {
        return new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'EUR'
        }).format(value / 100);
      }
    }
  }
  </script>
  