<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import FormModal from '../../Components/Modal/FormModal.vue';
import FormError from '../../Components/Forms/FormError.vue';
import PrimaryButton from '../../Components/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Components/Buttons/SecondaryButton.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    purchase: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const rebateOpen = ref(false);
const rebateForm = useForm({
    from_page: 'ProductPurchaseDetail',
    product_detail_id: '',
    rebate_qty: 1,
    rebate_reason: '',
    rebate_description: '',
});

function openRebate(item) {
    rebateForm.reset();
    rebateForm.clearErrors();
    rebateForm.from_page = 'ProductPurchaseDetail';
    rebateForm.product_detail_id = item.id;
    rebateForm.rebate_qty = 1;
    rebateOpen.value = true;
}

function submitRebate() {
    rebateForm.post(route('inertia.product-purchases.rebate'), {
        preserveScroll: true,
        onSuccess: () => {
            rebateOpen.value = false;
        },
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Management" :crumbs="['Home', 'ProductManagement', 'Purchases', 'Detail']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Product Purchase Detail</h4></div>
                    <div class="col-6 text-end">
                        <a :href="route('inertia.product-purchases.invoice', purchase)" class="btn btn-info btn-sm" target="_blank">
                            <i class="fa fa-file"></i> Invoice
                        </a>
                        <Link :href="route('inertia.product-purchases.index')" class="btn btn-secondary btn-sm ms-1">
                            <i class="fa fa-arrow-left"></i> Product Purchases
                        </Link>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Free Qty</th>
                            <th class="text-danger">Rebate Qty</th>
                            <th>Total Qty</th>
                            <th>Discount</th>
                            <th class="text-end">Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in purchase.items" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td><b>{{ item.product_code }}</b><br>{{ item.product_name }}</td>
                            <td>{{ item.product_purchase_price }}</td>
                            <td>{{ item.product_qty }}</td>
                            <td>{{ item.product_bonus_qty }}</td>
                            <td class="text-danger">{{ item.rebate_qty }}</td>
                            <td>{{ item.product_total_qty }}</td>
                            <td>{{ item.product_discount }}</td>
                            <td class="text-end">{{ item.product_total_price }}</td>
                            <td>
                                <button
                                    v-if="can('productPurchases.update') && Number(item.product_total_qty) > 0"
                                    type="button"
                                    class="btn btn-warning btn-sm"
                                    @click="openRebate(item)"
                                >
                                    Rebate
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <FormModal :show="rebateOpen" title="Record Rebate" @close="rebateOpen = false">
            <form autocomplete="off" @submit.prevent="submitRebate">
                <div class="mb-2">
                    <label>Rebate Qty*</label>
                    <input v-model="rebateForm.rebate_qty" class="form-control" type="number" min="1">
                    <FormError :message="rebateForm.errors.rebate_qty" />
                </div>
                <div class="mb-2">
                    <label>Reason</label>
                    <input v-model="rebateForm.rebate_reason" class="form-control" type="text">
                    <FormError :message="rebateForm.errors.rebate_reason" />
                </div>
                <div class="mb-2">
                    <label>Description</label>
                    <textarea v-model="rebateForm.rebate_description" class="form-control" rows="2"></textarea>
                </div>
                <PrimaryButton type="submit" :disabled="rebateForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="rebateOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
    </div>
</template>
