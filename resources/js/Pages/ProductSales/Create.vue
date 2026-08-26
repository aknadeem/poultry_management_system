<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import SaleForm from '../../Components/Products/SaleForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    divisions: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    productCategories: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Sale" :crumbs="['Home', 'ProductManagement', 'Sales', 'Create']" />
        <div class="row card">
            <div class="col-12 card-body">
                <SaleForm
                    form-title="Add Sale Entry"
                    :submit-url="route('inertia.product-sales.store')"
                    :index-url="route('inertia.product-sales.index')"
                    :divisions="divisions"
                    :customers="customers"
                    :companies="companies"
                    :product-categories="productCategories"
                />
            </div>
        </div>
    </div>
</template>
