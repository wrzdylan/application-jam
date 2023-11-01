<template>
  <v-container fluid>

    <v-alert class="mb-10" v-if="error" type="error" dismissible @dismissed="error = null">
      {{ error }}
    </v-alert>

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

    <!-- Bouton pour ouvrir la modale -->
    <v-btn @click="dialog = true">Voir le panier</v-btn>

    <!-- Modale pour afficher le panier -->
    <v-dialog v-model="dialog" max-width="500px">
      <v-card>
        <v-card-title class="d-flex justify-space-between">
          Mon panier

          <v-btn icon @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text>
          <v-list>
            <v-list-item-group>
              <v-list-item v-for="item in cart" :key="item.id">
        
                <v-list-item-content class="d-flex justify-space-between align-center">
                  <div>
                    <v-list-item-title>{{ item.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ item.quantity }} x {{ parseFloat(item.price / 100).toFixed(2) }}€ = {{ itemTotalPrice(item).toFixed(2) }}€
                    </v-list-item-subtitle>
                  </div>
        
                  <v-btn icon @click="decrementQuantity(item)">
                    <v-icon>mdi-minus</v-icon>
                  </v-btn>
                </v-list-item-content>
        
              </v-list-item>
            </v-list-item-group>
          </v-list>
        
          <v-divider class="my-4"></v-divider>
        
          Prix total du panier : {{ cartTotalPrice() }}€
        </v-card-text>
        
      </v-card>
    </v-dialog>

    <!-- Produits (filtrables) -->
    <v-main class="pl-0">
      <v-row>
        <v-col v-for="product in filteredProducts" :key="product.id" cols="12" sm="4">
          <v-card>
            <v-img :src="'/uploads/' + product.image" aspect-ratio="1.7"></v-img>
            <v-card-title>{{ product.name }}</v-card-title>
            <v-card-subtitle>{{ parseFloat(product.price / 100).toFixed(2) }}€</v-card-subtitle>
            <v-card-actions>
              <v-btn @click="addToCart(product)">Ajouter au panier</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-main>

    <v-snackbar
      v-model="alertVisible"
      :timeout="3000"
      color="success"
      :bottom="true"
      :width="'100%'"
    >
      <div class="d-flex align-center justify-space-between w-100">
        <span>{{ alertMessage }}</span>
        <v-btn
          color="white"
          text
          @click="alertVisible = false"
        >
          Fermer
        </v-btn>
      </div>
    </v-snackbar>
  </v-container>
</template>

  
<script>
import axios from '@/axios.js';

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
      maxPrice: null,
      cart: [],
      dialog: false,
      alertVisible: false,
      alertMessage: '',
      error: null,
    };
  },
  async mounted() {
    try {
      this.categories = await this.fetchData("http://localhost:8000/api/categories");
      this.products = await this.fetchData("http://localhost:8000/api/products");
      
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
        const cachedTimestamp = localStorage.getItem(url + '-timestamp');

        const currentTime = Date.now();
        const maxCacheAge = 60 * 60 * 1000;

        if (cachedData && cachedTimestamp && (currentTime - cachedTimestamp <= maxCacheAge)) {
          return JSON.parse(cachedData);
        }

        const response = await axios.get(url);
        const data = response.data['hydra:member']; 

        localStorage.setItem(url, JSON.stringify(data));
        localStorage.setItem(url + '-timestamp', currentTime.toString());

        return data;
      } catch (error) {
        console.error("Erreur de récupération des données :", error);
        this.error = "Une erreur est survenue lors de la récupération des données.";
        throw error;
      }
    },
    toggleCategory(categoryId, event) {
      console.log(categoryId)
      const index = this.selectedCategories.indexOf(categoryId);

      if (event && index === -1) {
        this.selectedCategories.push(categoryId);
      } else {
          this.selectedCategories.splice(index, 1);
      }
      console.log(this.selectedCategories)
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
    },
    addToCart(product) {
      const productInCart = this.cart.find(item => item.id === product.id);
      if (productInCart) {
        productInCart.quantity += 1;
      } else {
        this.cart.push({ ...product, quantity: 1 });
      }

      this.alertMessage = `${product.name} a été ajouté au panier`;
      this.alertVisible = true;
    },
    itemTotalPrice(item) {
      return (item.price / 100) * item.quantity;
    },
    cartTotalPrice() {
      return this.cart.reduce((total, item) => total + this.itemTotalPrice(item), 0).toFixed(2);
    },
    decrementQuantity(item) {
      const productInCart = this.cart.find(cartItem => cartItem.id === item.id);

      if (productInCart) {
        productInCart.quantity -= 1;

        if (productInCart.quantity === 0) {
          const index = this.cart.indexOf(productInCart);
          this.cart.splice(index, 1);
        }
      }
    },
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
  