<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    feed: { type: Object, required: true },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle :title="`${feed.category_name || 'Feed'} - ${feed.feed_name}`" :crumbs="['Home', 'Feed']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Feed Purchases</h4></div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.feeds.index')" class="btn btn-secondary btn-sm" title="Click to go back">
                            <i class="fa fa-arrow-left"></i> Back
                        </Link>
                    </div>
                </div>
                <table class="table table-striped dt-responsive w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Purchase Date</th>
                            <th>Company</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Discount Amount</th>
                            <th>Total Amount</th>
                            <th>Sale Order Number</th>
                            <th>Delivery Order Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in feed.purchases" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td><b class="text-danger">{{ item.purchase_date_label }}</b></td>
                            <td><b>{{ item.company_name }}</b></td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.price }}</td>
                            <td>{{ item.discount_amount }}</td>
                            <td><b>{{ item.total_price }}</b></td>
                            <td>{{ item.sale_order_number }}</td>
                            <td>{{ item.delivery_order_number }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
