<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    purchase: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicks Purchase" :crumbs="['Home', 'ChicksPurchases', 'Show']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Purchase Detail</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('chickPurchases.update')" :href="route('inertia.chick-purchases.edit', purchase)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <Link :href="route('inertia.chick-purchases.index')" class="btn btn-secondary btn-sm ms-1">
                            <i class="fa fa-arrow-left"></i> Purchases
                        </Link>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-2"><label>Purchase Date</label><p class="mb-0">{{ purchase.purchase_date_label }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Chick Grade</label><p class="mb-0">{{ purchase.chick_grade_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Company</label><p class="mb-0">{{ purchase.company_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Vendor</label><p class="mb-0">{{ purchase.vendor_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Customer</label><p class="mb-0">{{ purchase.customer_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Farm Name</label><p class="mb-0">{{ purchase.customer_farm_name || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Chick Age</label><p class="mb-0">{{ purchase.chick_entry_age }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Weight (gm)</label><p class="mb-0">{{ purchase.chick_weight }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Quantity</label><p class="mb-0">{{ purchase.quantity }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Price</label><p class="mb-0">{{ purchase.price }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Discount Amount</label><p class="mb-0">{{ purchase.discount_amount }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Total Price</label><p class="mb-0"><b>{{ purchase.total_price }}</b></p></div>
                    <div class="col-sm-3 mb-2"><label>Vehicle Number</label><p class="mb-0">{{ purchase.vehicle_number || '—' }}</p></div>
                    <div class="col-sm-3 mb-2"><label>Driver Name</label><p class="mb-0">{{ purchase.driver_name || '—' }}</p></div>
                    <div v-if="purchase.picture_url" class="col-sm-6 mb-2">
                        <label>Invoice Picture</label>
                        <p class="mb-0">
                            <a :href="purchase.picture_url" target="_blank">
                                <img :src="purchase.picture_url" alt="Purchase" class="avatar-lg">
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
