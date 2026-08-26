<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    rebates: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Sale Rebates" :crumbs="['Home', 'ProductManagement', 'Sales', 'Rebates']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Sale Rebates</h4></div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.product-sales.index')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </Link>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sale</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Reason</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rebate in rebates" :key="rebate.id">
                            <td>{{ rebate.rebate_item_id }}</td>
                            <td>{{ rebate.product_id }}</td>
                            <td>{{ rebate.rebate_qty }}</td>
                            <td>{{ rebate.rebate_reason }}</td>
                            <td>{{ rebate.rebate_description }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
