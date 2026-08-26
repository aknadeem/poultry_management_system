<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ProductStoreForm from '../../Components/ProductStores/ProductStoreForm.vue';
import { useRoute } from '../../Utils/route';

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="ProductManagement" :crumbs="['Home', 'ProductManagement', 'Product Stores', 'Create']" />
        <div class="card">
            <div class="card-body">
                <ProductStoreForm
                    form-title="Add Store Entry"
                    :submit-url="route('inertia.product-stores.store')"
                    :index-url="route('inertia.product-stores.index')"
                />
            </div>
        </div>
    </div>
</template>
