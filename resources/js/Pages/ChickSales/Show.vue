<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    sale: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicken Sale" :crumbs="['Home', 'ChickensSales', 'Show']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Sale Detail</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('chickSales.update')" :href="route('inertia.chick-sales.edit', sale)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <Link :href="route('inertia.chick-sales.index')" class="btn btn-secondary btn-sm ms-1">
                            <i class="fa fa-arrow-left"></i> Sales
                        </Link>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-2"><label>Manual Number</label><p class="mb-0">{{ sale.manual_number || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Sale Date</label><p class="mb-0">{{ sale.sale_date_label }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Broker</label><p class="mb-0">{{ sale.broker_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Customer</label><p class="mb-0">{{ sale.customer_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Customer Farm</label><p class="mb-0">{{ sale.customer_farm_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Customer Contact</label><p class="mb-0">{{ sale.customer_contact || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>First Weight</label><p class="mb-0">{{ sale.first_weight }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Second Weight</label><p class="mb-0">{{ sale.second_weight }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Net Weight</label><p class="mb-0">{{ sale.net_weight }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Total Weight</label><p class="mb-0">{{ sale.total_weight }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Per/Kg Price</label><p class="mb-0">{{ sale.per_kg_price }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Discount Amount</label><p class="mb-0">{{ sale.discount_amount }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Total Price</label><p class="mb-0"><b>{{ sale.total_price }}</b></p></div>
                    <div class="col-sm-3 mb-2"><label>Vehicle Number</label><p class="mb-0">{{ sale.vehicle_number || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Driver Name</label><p class="mb-0">{{ sale.driver_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Driver Contact</label><p class="mb-0">{{ sale.driver_contact || '—' }}</p></div>
                    <div v-if="sale.picture_url" class="col-sm-6 mb-2">
                        <label>Invoice Picture</label>
                        <p class="mb-0">
                            <a :href="sale.picture_url" target="_blank">
                                <img :src="sale.picture_url" alt="Sale" class="avatar-lg">
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
