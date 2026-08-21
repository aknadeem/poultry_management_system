<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PurchaseForm from '../../Components/Products/PurchaseForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    productGroups: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    productCategories: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Purchase" :crumbs="['Home', 'ProductManagement', 'Purchases', 'Create']" />
        <div class="row card">
            <div class="col-12 card-body">
                <PurchaseForm
                    form-title="Add Purchase Entry"
                    :submit-url="route('inertia.product-purchases.store')"
                    :index-url="route('inertia.product-purchases.index')"
                    :product-groups="productGroups"
                    :companies="companies"
                    :product-categories="productCategories"
                />
            </div>
        </div>
    </div>
</template>
