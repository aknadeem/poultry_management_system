<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ProductForm from '../../Components/Products/ProductForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    productGroups: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    productCategories: { type: Array, default: () => [] },
    productTypes: { type: Array, default: () => [] },
    productStores: { type: Array, default: () => [] },
    vaccinationGroups: { type: Array, default: () => [] },
    packSizeUnitTypes: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="ProductManagement" :crumbs="['Home', 'ProductManagement', 'Products', 'Create']" />
        <div class="card">
            <div class="card-body">
                <ProductForm
                    form-title="Add Product Entry"
                    :submit-url="route('inertia.products.store')"
                    :index-url="route('inertia.products.index')"
                    :product-groups="productGroups"
                    :companies="companies"
                    :product-categories="productCategories"
                    :product-types="productTypes"
                    :product-stores="productStores"
                    :vaccination-groups="vaccinationGroups"
                    :pack-size-unit-types="packSizeUnitTypes"
                />
            </div>
        </div>
    </div>
</template>
