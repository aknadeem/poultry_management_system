<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    purchase: { type: Object, default: null },
    formTitle: { type: String, default: 'Create Purchase' },
    chickGrades: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    today: { type: String, default: '' },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const route = useRoute();
const page = usePage();
const isCreate = computed(() => props.purchase === null);
const chickGrades = ref([...(props.chickGrades ?? [])]);
const lookupOpen = ref(false);
const lookupSelectField = ref('');
const priceQtyError = ref('');
const discountAmountError = ref('');
const discountPercentageError = ref('');
const chickAges = Array.from({ length: 10 }, (_, index) => index + 1);

const lookupForm = useForm({
    tag_name: 'chick_grades',
    name: '',
});

const form = useForm({
    purchase_date: props.purchase?.purchase_date ?? props.today,
    chick_grade_id: props.purchase?.chick_grade_id ?? '',
    company_id: props.purchase?.company_id ?? '',
    vendor_id: props.purchase?.vendor_id ?? '',
    vendor_name: props.purchase?.vendor_name ?? '',
    customer_id: props.purchase?.customer_id ?? '',
    customer_farm_id: props.purchase?.customer_farm_id ?? '',
    customer_farm_name: props.purchase?.customer_farm_name ?? '',
    personal_farm_capacity: props.purchase?.personal_farm_capacity ?? '',
    Personal_farm_address: props.purchase?.personal_farm_address ?? '',
    chick_entry_age: props.purchase?.chick_entry_age ?? '',
    chick_weight: props.purchase?.chick_weight ?? '',
    quantity: props.purchase?.quantity ?? '',
    price: props.purchase?.price ?? '',
    discount_amount: props.purchase?.discount_amount ?? 0,
    discount_percentage: props.purchase?.discount_percentage ?? 0,
    total_price: props.purchase?.total_price ?? '',
    bilty_number: props.purchase?.bilty_number ?? '',
    bilty_charges: props.purchase?.bilty_charges ?? '',
    vehicle_number: props.purchase?.vehicle_number ?? '',
    driver_name: props.purchase?.driver_name ?? '',
    driver_contact: props.purchase?.driver_contact ?? '',
    sale_order_number: props.purchase?.sale_order_number ?? '',
    delivery_order_number: props.purchase?.delivery_order_number ?? '',
    remarks: props.purchase?.remarks ?? '',
    image_file: null,
});

watch(() => form.company_id, (companyId) => {
    const company = props.companies.find((item) => Number(item.id) === Number(companyId));
    form.vendor_id = company?.vendor?.id || '';
    form.vendor_name = company?.vendor?.name || '';
});

watch(() => form.customer_id, (customerId) => {
    const customer = props.customers.find((item) => Number(item.id) === Number(customerId));
    form.customer_farm_id = customer?.farm?.id || '';
    form.customer_farm_name = customer?.farm?.farm_name || '';
    form.personal_farm_capacity = customer?.farm?.farm_capacity || '';
    form.Personal_farm_address = customer?.farm?.farm_address || '';
});

function basePrice() {
    const quantity = Number(form.quantity);
    const price = parseFloat(form.price);

    return quantity > 0 && price > 0 ? quantity * price : 0;
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

function openLookup() {
    lookupForm.reset();
    lookupForm.clearErrors();
    lookupForm.tag_name = 'chick_grades';
    lookupSelectField.value = 'chick_grade_id';
    lookupOpen.value = true;
}

function submitLookup() {
    lookupForm.post(route('inertia.farm-lookup-types.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const created = page.props.flash?.createdLookup;
            if (created?.id) {
                if (! chickGrades.value.some((item) => Number(item.id) === Number(created.id))) {
                    chickGrades.value = [...chickGrades.value, { id: created.id, name: created.name }];
                }
                form.chick_grade_id = created.id;
            }
            lookupOpen.value = false;
        },
    });
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
                <Link :href="indexUrl" class="btn btn-secondary btn-sm" title="Click to see Purchases">
                    <i class="fa fa-arrow-left"></i> Purchases
                </Link>
            </div>
        </div>
        <form autocomplete="off" enctype="multipart/form-data" @submit.prevent="submit">
            <div class="row form-group mt-2 fs-5">
                <div class="col-sm-3 mb-2">
                    <label>Purchase Date *</label>
                    <input v-model="form.purchase_date" class="form-control" type="date" required>
                    <FormError :message="form.errors.purchase_date" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold">Select Chick Grade*</label>
                    <div class="input-group">
                        <select v-model="form.chick_grade_id" class="form-control">
                            <option value="">Select chick grade</option>
                            <option v-for="grade in chickGrades" :key="grade.id" :value="grade.id">{{ grade.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark btn-sm" title="Click to add new" @click.prevent="openLookup">
                            <i class="fa fa-plus pt-1"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.chick_grade_id" />
                </div>
                <div class="col-3 mb-2">
                    <label>Select Company *</label>
                    <select v-model="form.company_id" class="form-control" required>
                        <option value="">Select Company</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.company_name }}</option>
                    </select>
                    <FormError :message="form.errors.company_id" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Vendor Name</label>
                    <input v-model="form.vendor_name" class="form-control" type="text" readonly disabled>
                    <FormError :message="form.errors.vendor_name" />
                </div>
            </div>
            <div class="row fs-5">
                <div class="col-3 mb-2">
                    <label>Select customer *</label>
                    <select v-model="form.customer_id" class="form-control" required>
                        <option value="" disabled>Select Customer</option>
                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                            {{ customer.name }} [{{ customer.cnic_no }}]
                        </option>
                    </select>
                    <FormError :message="form.errors.customer_id" />
                </div>
                <div class="col-3 mb-2">
                    <label>Farm Name *</label>
                    <input v-model="form.customer_farm_name" class="form-control" type="text" readonly>
                    <FormError :message="form.errors.customer_farm_name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Farm Capacity</label>
                    <input v-model="form.personal_farm_capacity" class="form-control" type="number" step="any" min="0" readonly>
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Farm Address</label>
                    <input v-model="form.Personal_farm_address" class="form-control" type="text" readonly>
                </div>
            </div>
            <div class="row fs-5">
                <div class="col-sm-3 mb-2">
                    <label>Select Chick Age</label>
                    <select v-model="form.chick_entry_age" class="form-control" required>
                        <option value="" disabled>Select chick age</option>
                        <option v-for="age in chickAges" :key="age" :value="age">{{ age > 1 ? `${age} Days` : `${age} Day` }}</option>
                    </select>
                    <FormError :message="form.errors.chick_entry_age" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Weight in Grams (gm)</label>
                    <input v-model="form.chick_weight" class="form-control" type="number" step="any" min="0" max="50" required placeholder="Enter weight">
                    <FormError :message="form.errors.chick_weight" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Quantity</label>
                    <input v-model="form.quantity" class="form-control" type="number" step="any" min="0" required placeholder="Enter quantity" @keyup="recalcPrice">
                    <FormError :message="form.errors.quantity" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Price</label>
                    <input v-model="form.price" class="form-control" type="number" min="0" step="any" required placeholder="Enter price" @keyup="recalcPrice">
                    <FormError :message="form.errors.price" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Discount Amount</label>
                    <input v-model="form.discount_amount" class="form-control" type="number" min="0" step="any" placeholder="Discount Amount" @keyup="applyDiscountAmount">
                    <FormError :message="form.errors.discount_amount" />
                    <span class="text-danger">{{ discountAmountError }}</span>
                </div>
                <span v-if="priceQtyError" class="text-danger h6">{{ priceQtyError }}</span>
                <div class="col-3 mb-2">
                    <label>Discount Percentage %</label>
                    <input v-model="form.discount_percentage" class="form-control" type="number" min="0" step="any" placeholder="Discount Percentage %" @keyup="applyDiscountPercentage">
                    <FormError :message="form.errors.discount_percentage" />
                    <span class="text-danger">{{ discountPercentageError }}</span>
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Total Price</label>
                    <input v-model="form.total_price" class="form-control" type="number" min="0" step="any" placeholder="Total price">
                    <FormError :message="form.errors.total_price" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Bilty Number</label>
                    <input v-model="form.bilty_number" class="form-control" type="text" placeholder="Enter Bilty Number">
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Bilty Charges</label>
                    <input v-model="form.bilty_charges" class="form-control" type="number" min="0" step="any" placeholder="Enter Bilty Charges">
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
                    <input v-model="form.driver_contact" class="form-control" type="number" min="0" placeholder="Driver number">
                    <FormError :message="form.errors.driver_contact" />
                </div>
                <div class="col-3 mb-2">
                    <label>Sale Order Number</label>
                    <input v-model="form.sale_order_number" class="form-control" type="text" placeholder="Enter Sale Order Number">
                </div>
                <div class="col-3 mb-2">
                    <label>Delivery Order Number</label>
                    <input v-model="form.delivery_order_number" class="form-control" type="text" placeholder="Enter Delivery Order Number">
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Remarks</label>
                    <input v-model="form.remarks" class="form-control" type="text" placeholder="Enter Remarks If any">
                </div>
                <div class="col-sm-3 mb-2">
                    <label>Invoice Picture</label>
                    <input class="form-control" type="file" accept="image/png,image/jpg,image/jpeg" @change="form.image_file = $event.target.files[0] ?? null">
                    <FormError :message="form.errors.image_file" />
                </div>
                <div v-if="! isCreate && purchase?.picture_url" class="col-sm-6 mt-2 img-holder">
                    <a :href="purchase.picture_url" target="_blank">
                        <img class="d-flex me-3 avatar-lg" :src="purchase.picture_url" alt="No image">
                    </a>
                </div>
            </div>
            <div class="row form-group fs-5">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                    <Link :href="indexUrl" class="ms-1"><SecondaryButton type="button">Cancel</SecondaryButton></Link>
                </div>
            </div>
        </form>
        <FormModal :show="lookupOpen" title="Add new" size="md" @close="lookupOpen = false">
            <form autocomplete="off" @submit.prevent="submitLookup">
                <div class="mb-2">
                    <label>Name *</label>
                    <input v-model="lookupForm.name" class="form-control" type="text">
                    <FormError :message="lookupForm.errors.name" />
                </div>
                <PrimaryButton type="submit" :disabled="lookupForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="lookupOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
    </div>
</template>
