<template>
  <v-container>
    <v-row>
      <!-- Catégories -->
      <v-col v-for="category in categories" :key="category.id" cols="12" sm="3">
        <v-checkbox v-model="selectedCategories" :label="category.name" :value="category.id"></v-checkbox>
      </v-col>

      <!-- Produits (filtrables) -->
      <v-col cols="12" sm="9">
        <v-row>
          <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="4">
            <v-card>
              <v-img :src="product.imageUrl" aspect-ratio="1.7"></v-img>
              <v-card-title>{{ product.name }}</v-card-title>
              <v-card-subtitle>{{ currency(product.price) }}</v-card-subtitle>
              <!-- Formulaire d'action produit (par exemple, ajouter au panier) ici -->
            </v-card>
          </v-col>
        </v-row>
      </v-col>

    </v-row>
  </v-container>
</template>
  
  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        products: [], 
        categories: [], 
        // filters: [
        //   { label: 'Prix croissant', value: 'price_asc' },
        //   { label: 'Prix décroissant', value: 'price_desc' },
        //   { label: 'Alphabétique', value: 'name_asc' },
        //   { label: 'Alphabétique Z->A', value: 'name_desc' }
        // ],
        // selectedFilter: 'price_asc',
        selectedCategories: []
      };
    },
    methods: {
      async fetchData(url) {
        try {
          const cachedData = localStorage.getItem(url);

          if (cachedData) {
            return JSON.parse(cachedData);
          }

          const response = await axios.get(url);
          const data = response.data['hydra:member'];  // Accéder aux données avec la clé 'hydra:member'
          localStorage.setItem(url, JSON.stringify(data));
          return data;
        } catch (error) {
          console.error("Erreur de récupération des données :", error);
          throw error;
        }
      }
    },
    async mounted() {
      try {
        this.categories = await this.fetchData("http://localhost:8080/api/categories");
        this.products = await this.fetchData("http://localhost:8080/api/products");
        console.log(this.categories)
      } catch (error) {
        this.error = error.message;
      }
    },
    computed: {
      currency(value) {
        return new Intl.NumberFormat('en-US', {
          style: 'currency',
          currency: 'EUR'
        }).format(value / 100);
      },
      filteredProducts() {
        if (this.selectedCategories.length === 0) {
          return this.products;
        }
        return this.products.filter(product =>
          this.selectedCategories.includes(product.categoryId)
        );
      }
    }
  }
  </script>
  