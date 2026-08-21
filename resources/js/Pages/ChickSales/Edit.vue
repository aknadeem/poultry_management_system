<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ChickSaleForm from '../../Components/Inventory/ChickSaleForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    sale: { type: Object, required: true },
    customers: { type: Array, default: () => [] },
    brokers: { type: Array, default: () => [] },
    today: { type: String, default: '' },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicken Sale" :crumbs="['Home', 'ChickensSales', 'Update']" />
        <div class="card">
            <div class="card-body">
                <ChickSaleForm
                    form-title="Update Sale"
                    method="put"
                    :sale="sale"
                    :submit-url="route('inertia.chick-sales.update', props.sale)"
                    :index-url="route('inertia.chick-sales.index')"
                    :customers="customers"
                    :brokers="brokers"
                    :today="today"
                />
            </div>
        </div>
    </div>
</template>
