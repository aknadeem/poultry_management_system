<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';

const props = defineProps({
    sale: { type: Object, default: null },
    formTitle: { type: String, default: 'Create Sale' },
    customers: { type: Array, default: () => [] },
    brokers: { type: Array, default: () => [] },
    today: { type: String, default: '' },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const isCreate = computed(() => props.sale === null);
const firstWeightError = ref('');
const priceQtyError = ref('');
const discountAmountError = ref('');
const discountPercentageError = ref('');

const form = useForm({
    manual_number: props.sale?.manual_number ?? '',
    sale_date: props.sale?.sale_date ?? props.today,
    broker_id: props.sale?.broker_id ?? '',
    customer_id: props.sale?.customer_id ?? '',
    customer_farm_name: props.sale?.customer_farm_name ?? '',
    customer_contact: props.sale?.customer_contact ?? '',
    first_weight: props.sale?.first_weight ?? '',
    second_weight: props.sale?.second_weight ?? '',
    net_weight: props.sale?.net_weight ?? '',
    total_weight: props.sale?.total_weight ?? '',
    per_kg_price: props.sale?.per_kg_price ?? '',
    discount_amount: props.sale?.discount_amount ?? 0,
    discount_percentage: props.sale?.discount_percentage ?? 0,
    broker_commission: props.sale?.broker_commission ?? '',
    total_price: props.sale?.total_price ?? '',
    vehicle_number: props.sale?.vehicle_number ?? '',
    driver_name: props.sale?.driver_name ?? '',
    driver_contact: props.sale?.driver_contact ?? '',
    image_file: null,
});

watch(() => form.customer_id, (customerId) => {
    const customer = props.customers.find((item) => Number(item.id) === Number(customerId));
    form.customer_contact = customer?.contact_no || '';
    form.customer_farm_name = customer?.farm?.farm_name || '';
});

function recalcWeights() {
    const firstWeight = parseFloat(form.first_weight);
    const secondWeight = parseFloat(form.second_weight);

    if (! (firstWeight > 0)) {
        firstWeightError.value = 'Please Enter First Weight, and should be greater then 0';
        return;
    }

    if (firstWeight > secondWeight) {
        firstWeightError.value = 'Please make sure, first weight should be less then Second weight';
        return;
    }

    firstWeightError.value = '';
    const netWeight = secondWeight - firstWeight;
    form.net_weight = netWeight;
    form.total_weight = netWeight;
    recalcPrice();
}

function basePrice() {
    const totalWeight = Number(form.total_weight);
    const perKgPrice = parseFloat(form.per_kg_price);

    return totalWeight > 0 && perKgPrice > 0 ? totalWeight * perKgPrice : 0;
}

function recalcPrice() {
    const priceWithoutDiscount = basePrice();
    if (priceWithoutDiscount > 0) {
        priceQtyError.value = '';
        form.total_price = priceWithoutDiscount;
        return;
    }

    priceQtyError.value = 'Price and Quantity Is required';
    form.total_price = '';
}

function applyDiscountAmount() {
    const discountAmount = parseFloat(form.discount_amount);
    const priceWithoutDiscount = basePrice();

    if (! (discountAmount > 0)) {
        form.discount_percentage = 0;
        form.total_price = priceWithoutDiscount || '';
        discountAmountError.value = '';
        return;
    }

    if (! (priceWithoutDiscount > 0)) {
        priceQtyError.value = 'Price and Quantity Is required';
        return;
    }

    priceQtyError.value = '';
    if (discountAmount >= priceWithoutDiscount) {
        discountAmountError.value = 'Discount Amount Must be less than total amount';
        return;
    }

    discountAmountError.value = '';
    form.discount_percentage = ((discountAmount / priceWithoutDiscount) * 100).toFixed(2);
    form.total_price = priceWithoutDiscount - discountAmount;
}

function applyDiscountPercentage() {
    const discountPercentage = parseFloat(form.discount_percentage) || 0;
    const priceWithoutDiscount = basePrice();
    discountPercentageError.value = '';

    if (discountPercentage > 0 && discountPercentage < 100) {
        if (! (priceWithoutDiscount > 0)) {
            priceQtyError.value = 'Price and Quantity Is required';
            return;
        }

        priceQtyError.value = '';
        const discountAmount = (discountPercentage / 100) * priceWithoutDiscount;
        form.discount_amount = discountAmount.toFixed(2);
        form.total_price = priceWithoutDiscount - discountAmount;
        return;
    }

    form.discount_amount = 0;
    form.total_price = priceWithoutDiscount || '';
    if (discountPercentage >= 100) {
        discountPercentageError.value = 'Discount Percentage Must be less than 100 (Total Amount)';
    }
}

function submit() {
    form.submit(props.method, props.submitUrl, { forceFormData: true });
}
</script>

<template>
    <div>
        <div class="row mb-2">
            <div class="col-6 align-self-start">
                <h4>{{ formTitle }}</h4>
            </div>
            <div class="col-6 align-self-end text-end mb-2">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm" title="Click to see Sales">
                    <i class="fa fa-arrow-left"></i> Sales
                </Link>
            </div>
        </div>
        <form autocomplete="off" enctype="multipart/form-data" @submit.prevent="submit">
            <div class="row form-group mt-2">
                <div class="col-sm-3 mb-2">
                    <label>Manual Number</label>
                    <input v-model="form.manual_number" class="form-control" type="text" required placeholder="Enter manual number">
                    <FormError :message="form.errors.manual_number" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Sale Date *</label>
                    <input v-model="form.sale_date" class="form-control" type="date" required>
                    <FormError :message="form.errors.sale_date" />
                </div>
                <div class="col-3 mb-2">
                    <label>Select Broker *</label>
                    <select v-model="form.broker_id" class="form-control" required>
                        <option value="">Select Broker</option>
                        <option v-for="broker in brokers" :key="broker.id" :value="broker.id">{{ broker.name }}</option>
                    </select>
                    <FormError :message="form.errors.broker_id" />
                </div>
                <div class="col-3 mb-2">
                    <label>Select Customer *</label>
                    <select v-model="form.customer_id" class="form-control" required>
                        <option value="">Select Customer</option>
                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option>
                    </select>
                    <FormError :message="form.errors.customer_id" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Customer Farm</label>
                    <input v-model="form.customer_farm_name" class="form-control" type="text" readonly disabled>
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Customer Contact</label>
                    <input v-model="form.customer_contact" class="form-control" type="text" readonly disabled>
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Fisrt Weight (kg)</label>
                    <input v-model="form.first_weight" class="form-control" type="number" step="any" min="1" required placeholder="Enter weight in Kilo Gram (kg)" @keyup="recalcWeights">
                    <FormError :message="form.errors.first_weight" />
                    <span class="text-danger">{{ firstWeightError }}</span>
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Second Weight (kg)</label>
                    <input v-model="form.second_weight" class="form-control" type="number" step="any" min="1" required placeholder="Enter weight in Kilo Gram (kg)" @keyup="recalcWeights">
                    <FormError :message="form.errors.second_weight" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Net Weight (kg)</label>
                    <input v-model="form.net_weight" class="form-control" type="number" step="any" min="1" readonly>
                    <FormError :message="form.errors.net_weight" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Total Weight (kg)</label>
                    <input v-model="form.total_weight" class="form-control" type="number" step="any" min="1" readonly>
                    <FormError :message="form.errors.total_weight" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Per/Kg Price</label>
                    <input v-model="form.per_kg_price" class="form-control" type="number" step="any" min="1" required placeholder="Enter price" @keyup="recalcPrice">
                    <FormError :message="form.errors.per_kg_price" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Discount Amount</label>
                    <input v-model="form.discount_amount" class="form-control" type="number" step="any" min="0" placeholder="Discount Amount" @keyup="applyDiscountAmount">
                    <FormError :message="form.errors.discount_amount" />
                    <span class="text-danger">{{ discountAmountError }}</span>
                </div>
                <span v-if="priceQtyError" class="text-danger h6">{{ priceQtyError }}</span>
                <div class="col-3 mb-2">
                    <label>Discount Percentage %</label>
                    <input v-model="form.discount_percentage" class="form-control" type="number" step="any" min="0" placeholder="Discount Percentage %" @keyup="applyDiscountPercentage">
                    <FormError :message="form.errors.discount_percentage" />
                    <span class="text-danger">{{ discountPercentageError }}</span>
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Broker Commission</label>
                    <input v-model="form.broker_commission" class="form-control" type="number" step="any" min="0" placeholder="Enter Broker Commission">
                    <FormError :message="form.errors.broker_commission" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Total Price</label>
                    <input v-model="form.total_price" class="form-control" type="number" step="any" min="1" placeholder="Total price">
                    <FormError :message="form.errors.total_price" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Vehicle Number</label>
                    <input v-model="form.vehicle_number" class="form-control" type="text" placeholder="Vehicle number">
                    <FormError :message="form.errors.vehicle_number" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Driver Name</label>
                    <input v-model="form.driver_name" class="form-control" type="text" placeholder="Driver number">
                    <FormError :message="form.errors.driver_name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Driver Contact</label>
                    <input v-model="form.driver_contact" class="form-control" type="text" placeholder="Driver number">
                    <FormError :message="form.errors.driver_contact" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Invoice Picture</label>
                    <input class="form-control" type="file" accept="image/png,image/jpg,image/jpeg" @change="form.image_file = $event.target.files[0] ?? null">
                    <FormError :message="form.errors.image_file" />
                </div>
                <div v-if="! isCreate && sale?.picture_url" class="col-sm-6 mt-2 img-holder">
                    <a :href="sale.picture_url" target="_blank">
                        <img class="d-flex me-3 avatar-lg" :src="sale.picture_url" alt="No image">
                    </a>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                    <Link :href="indexUrl" class="ms-1"><SecondaryButton type="button">Cancel</SecondaryButton></Link>
                </div>
            </div>
        </form>
    </div>
</template>
