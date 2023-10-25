<template>
  <v-container fluid>

    <!-- Sidebar avec Catégories -->
    <v-navigation-drawer app fixed class="pr-5 pl-5">
      <div class="mb-5"></div>
      <v-subheader>Trier par</v-subheader>
      <v-select
        v-model="selectedSort"
        :items="sortOptions"
        item-title="text"
        item-value="value"
        label="Trier par"
      ></v-select>

      <v-divider class="mb-5"></v-divider>

      <v-subheader>Catégories</v-subheader>
      <v-checkbox 
        v-for="category in categories"
        :key="category.id"
        :label="category.name"
        @change="toggleCategory(category.id, $event)">
      </v-checkbox>

      <v-divider class="mb-5"></v-divider>

      <v-subheader>Prix</v-subheader>
      <div class="mb-10"></div>
      <v-range-slider
        v-model="priceRange"
        :max="maxPrice"
        :min="minPrice"
        step="1"
        thumb-label="always"
      ></v-range-slider>
    </v-navigation-drawer>

    <!-- Produits (filtrables) -->
    <v-main class="pl-0">
      <v-row>
        <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="4">
          <v-card>
            <v-img :src="'/uploads/' + product.image" aspect-ratio="1.7"></v-img>
            <v-card-title>{{ product.name }}</v-card-title>
            <v-card-subtitle>{{ parseFloat(product.price / 100).toFixed(2) }}€</v-card-subtitle>
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
        selectedSort: { text: 'Prix croissant', value: 'price_asc' },
        sortOptions: [
          { text: 'Prix croissant', value: 'price_asc' },
          { text: 'Prix décroissant', value: 'price_desc' },
          { text: 'Nom A->Z', value: 'name_asc' },
          { text: 'Nom Z->A', value: 'name_desc' }
        ],
        selectedCategories: [],
        priceRange: [],
        minPrice: null,
        maxPrice: null
      };
    },
    
    async mounted() {
      try {
        this.categories = await this.fetchData("http://localhost:8080/api/categories");
        this.products = await this.fetchData("http://localhost:8080/api/products");
        
        const priceRange = this.products.reduce((acc, product) => {
          return {
            min: Math.min(acc.min, product.price),
            max: Math.max(acc.max, product.price)
          };
        }, { min: Infinity, max: 0 });
        
        this.minPrice = (Math.floor(priceRange.min / 10) * 10) / 100;
        this.maxPrice = (Math.ceil(priceRange.max / 10) * 10) / 100;
        this.priceRange = [this.minPrice, this.maxPrice];       
      } catch (error) {
        this.error = error.message;
      }
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
      toggleCategory(categoryId, event) {
        const index = this.selectedCategories.indexOf(categoryId);

        if (event && index === -1) {
          this.selectedCategories.push(categoryId);
        } else {
            this.selectedCategories.splice(index, 1);
        }
      },
      extractCategoryId(categoryUrl) {
        const segments = categoryUrl.split('/');
        return segments[segments.length - 1];
      },
      sortProducts(products) {
        switch (this.selectedSort) {
          case 'price_asc':
            return products.slice().sort((a, b) => a.price - b.price);
          case 'price_desc':
            return products.slice().sort((a, b) => b.price - a.price);
          case 'name_asc':
            return products.slice().sort((a, b) => a.name.localeCompare(b.name));
          case 'name_desc':
            return products.slice().sort((a, b) => b.name.localeCompare(a.name));
          default:
            return products;  // retourner la liste non triée si aucun tri n'est sélectionné
        }
      }
    },
    computed: {
      filteredProducts() {
        let products = this.products.filter(product => {
          const productPrice = product.price / 100;
          if (productPrice < this.priceRange[0] || productPrice > this.priceRange[1]) {
            return false;
          }
          
          if (this.selectedCategories.length === 0) {
            return true;
          }

          const productCategoryIds = product.categories.map(categoryUrl =>
            this.extractCategoryId(categoryUrl)
          );
          return productCategoryIds.some(categoryId =>
            this.selectedCategories.includes(Number(categoryId))
          );
        });

        products = this.sortProducts(products);
        return products;
      },
    }
  }
  </script>
  