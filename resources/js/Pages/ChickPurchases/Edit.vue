<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ChickPurchaseForm from '../../Components/Inventory/ChickPurchaseForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    purchase: { type: Object, required: true },
    chickGrades: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    today: { type: String, default: '' },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicks Purchase" :crumbs="['Home', 'ChicksPurchases', 'Update']" />
        <div class="card">
            <div class="card-body">
                <ChickPurchaseForm
                    form-title="Update Purchase"
                    method="put"
                    :purchase="purchase"
                    :submit-url="route('inertia.chick-purchases.update', props.purchase)"
                    :index-url="route('inertia.chick-purchases.index')"
                    :chick-grades="chickGrades"
                    :companies="companies"
                    :customers="customers"
                    :today="today"
                />
            </div>
        </div>
    </div>
</template>
