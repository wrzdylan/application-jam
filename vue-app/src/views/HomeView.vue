<template>
  <v-container fluid>

    <!-- Sidebar avec Catégories -->
    <v-navigation-drawer app fixed>
      <v-list dense>
        <v-list-item-group v-model="selectedCategories">
          <v-list-item v-for="category in categories" :key="category.id" :value="category.id">
            <v-list-item-content>
              <v-simple-checkbox :value="selectedCategories.includes(category.id)" @input="toggleCategory(category.id, $event)"></v-simple-checkbox>
              <v-list-item-title>{{ category.name }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
    

    <!-- Produits (filtrables) -->
    <v-main class="pl-0">
      <v-row>
        <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="4">
          <v-card>
            <v-img :src="'/uploads/' + product.image" aspect-ratio="1.7"></v-img>
            <v-card-title>{{ product.name }}</v-card-title>
            <v-card-subtitle>{{ product.price }}</v-card-subtitle>
            <!-- Formulaire d'action produit (par exemple, ajouter au panier) ici -->
          </v-card>
        </v-col>
      </v-row>
    </v-main>

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
      },
      toggleCategory(categoryId, value) {
        if (value) {
          this.selectedCategories.push(categoryId);
        } else {
          const index = this.selectedCategories.indexOf(categoryId);
          if (index !== -1) {
            this.selectedCategories.splice(index, 1);
          }
        }
      },
      extractCategoryId(categoryUrl) {
        const segments = categoryUrl.split('/');
        return segments[segments.length - 1];
      }
    },
    async mounted() {
      try {
        this.categories = await this.fetchData("http://localhost:8080/api/categories");
        this.products = await this.fetchData("http://localhost:8080/api/products");
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
          // Vérifier si au moins une des catégories du produit est sélectionnée
          product.categories.some(categoryUrl =>
            this.selectedCategories.includes(this.extractCategoryId(categoryUrl))
          )
        );
      }
    }
  }
  </script>
  