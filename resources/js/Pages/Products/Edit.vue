<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ProductForm from '../../Components/Products/ProductForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    product: { type: Object, required: true },
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
        <PageTitle title="ProductManagement" :crumbs="['Home', 'ProductManagement', 'Products', 'Edit']" />
        <div class="card">
            <div class="card-body">
                <ProductForm
                    form-title="Edit Product Entry"
                    method="put"
                    :product="product"
                    :submit-url="route('inertia.products.update', props.product)"
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
