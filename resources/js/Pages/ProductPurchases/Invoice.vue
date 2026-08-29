<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    purchase: { type: Object, required: true },
});

const route = useRoute();

const discountPercent = computed(() => {
    const total = Number(props.purchase.total_amount || 0);
    const discount = Number(props.purchase.discount_amount || 0);
    if (! total) {
        return '0.00';
    }

    return ((discount / total) * 100).toFixed(2);
});

function money(value) {
    return Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function printInvoice() {
    window.print();
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Invoice" :crumbs="['Home', 'ProductManagement', 'ProductPurchase', 'Invoice']" />
        <div class="card">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-start">
                        <img src="/assets/images/logo/poultryLogo.png" alt="Logo" style="max-width: 50%;">
                    </div>
                    <div class="float-end">
                        <h4 class="m-0 d-print-none">Invoice</h4>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-6">
                        <p class="fs-5 mb-1"><b>{{ purchase.company_name }}</b></p>
                        <p class="text-muted">Purchase invoice</p>
                    </div>
                    <div class="col-6">
                        <div class="float-end">
                            <p class="mb-1"><strong>Purchase Date :</strong> <span class="float-end">{{ purchase.purchase_date_label }}</span></p>
                            <p class="mb-1">
                                <strong>Payment Status :</strong>
                                <span class="float-end">
                                    <span class="badge" :class="`bg-${purchase.payment_status_color || 'secondary'}`">
                                        {{ purchase.payment_status_label }}
                                    </span>
                                </span>
                            </p>
                            <p class="mb-1"><strong>Purchase code :</strong> <span class="float-end">{{ purchase.purchase_code }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-2">
                    <table class="table table-centered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th style="width: 10%">Price</th>
                                <th style="width: 10%">Qty</th>
                                <th style="width: 10%">Free Qty</th>
                                <th style="width: 10%">Discount</th>
                                <th style="width: 10%" class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in purchase.items || []" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <b class="fs-5">{{ item.product_code }}</b><br>
                                    {{ item.product_name }}
                                </td>
                                <td>{{ money(item.product_purchase_price) }}</td>
                                <td>{{ item.product_qty }}</td>
                                <td>{{ item.product_bonus_qty }}</td>
                                <td>{{ money(item.product_discount) }}</td>
                                <td class="text-end">{{ money(item.product_total_price) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6">
                        <div class="float-end fs-5">
                            <p><b>Sub-total:</b> <span class="float-end">{{ money(purchase.total_amount) }}</span></p>
                            <p><b>Discount ({{ discountPercent }}%):</b> <span class="float-end">{{ money(purchase.discount_amount) }}</span></p>
                            <p><b>Other:</b> <span class="float-end">{{ money(purchase.other_charges) }}</span></p>
                            <h3 class="text-danger"><b>Total:</b> {{ money(purchase.final_amount) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-1 text-end d-print-none">
                    <button type="button" class="btn btn-primary me-1" @click="printInvoice">
                        <i class="mdi mdi-printer me-1"></i> Print
                    </button>
                    <Link :href="route('inertia.product-purchases.show', purchase)" class="btn btn-info">Go Back</Link>
                </div>
            </div>
        </div>
    </div>
</template>
