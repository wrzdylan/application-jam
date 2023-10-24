<!-- Search.vue -->
<template>
    <div class="row mt-4">
        <div class="col-12 mt-4" id="content">
            <div class="row">
                <div class="col-8">
                    <div class="row">
                        <div v-for="product in products" :key="product.id" class="m-2 col-3 m-22 card" :product="product.id">
                            <img class="p-4 card-img-top" :src="'/uploads/' + product.image">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title">{{ product.name }}</h5>
                                <p class="card-text">
                                    {{ (product.price/100).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }) }}
                                </p>
                                <div class="row">
                                    <form class="col" @submit.prevent="addToCart(product)">
                                        <input v-if="!cart[product.id]" class="quantity form-control" v-model="product.quantity" type="number" value="1">
                                        <input v-else class="quantity form-control" v-model="cart[product.id]" type="number">
                                        <button class="mt-2 btn btn-dark" type="submit">
                                            {{ cart[product.id] ? 'MODIFIER' : 'AJOUTER' }}
                                        </button>
                                    </form>
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
            cart: {}
        };
    },
    methods: {
        async addToCart(product) {
        }
    }
}
</script>

<style scoped>
</style>
