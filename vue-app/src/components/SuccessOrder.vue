<!-- SuccessOrder.vue -->
<template>
    <div class="row">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12">
                <h2>Merci {{ email }}</h2>
                <p>Commande numéro {{ orderId }} validée.</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        email: {
            type: String,
            required: true
        },
        orderId: {
            type: Number,
            required: true
        }
    }
}
</script>

<style scoped>
</style>
