<script setup>
import { ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    formTitle: { type: String, default: 'Add Feed Entry' },
    categories: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    today: { type: String, default: '' },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
});

const route = useRoute();
const page = usePage();
const categories = ref([...(props.categories ?? [])]);
const lookupOpen = ref(false);
const priceQtyError = ref('');
const discountAmountError = ref('');
const discountPercentageError = ref('');

const lookupForm = useForm({
    tag_name: 'feed_categories',
    name: '',
});

const form = useForm({
    feed_name: '',
    purchase_date: props.today,
    feed_category_id: '',
    company_id: '',
    vendor_name: '',
    quantity: '',
    price: '',
    discount_amount: 0,
    discount_percentage: 0,
    total_price: '',
    bilty_number: '',
    bilty_charges: '',
    per_bag_discount: '',
    sale_order_number: '',
    delivery_order_number: '',
    description: '',
    image_file: null,
});

watch(() => form.company_id, (companyId) => {
    const company = props.companies.find((item) => Number(item.id) === Number(companyId));
    form.vendor_name = company?.vendor?.name || '';
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
    lookupForm.tag_name = 'feed_categories';
    lookupOpen.value = true;
}

function submitLookup() {
    lookupForm.post(route('inertia.farm-lookup-types.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const created = page.props.flash?.createdLookup;
            if (created?.id) {
                if (! categories.value.some((item) => Number(item.id) === Number(created.id))) {
                    categories.value = [...categories.value, { id: created.id, name: created.name }];
                }
                form.feed_category_id = created.id;
            }
            lookupOpen.value = false;
        },
    });
}

function submit() {
    form.post(props.submitUrl, { forceFormData: true });
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
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form autocomplete="off" enctype="multipart/form-data" @submit.prevent="submit">
            <div class="row form-group">
                <div class="col-sm-4 mb-2">
                    <label>Feed Name *</label>
                    <input v-model="form.feed_name" class="form-control" type="text" placeholder="Enter feed name">
                    <FormError :message="form.errors.feed_name" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Purchase Date *</label>
                    <input v-model="form.purchase_date" class="form-control" type="date">
                    <FormError :message="form.errors.purchase_date" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold">Select category</label>
                    <div class="input-group">
                        <select v-model="form.feed_category_id" class="form-control">
                            <option value="">Select category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark" title="Click to add new" @click.prevent="openLookup">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.feed_category_id" />
                </div>
                <div class="col-4 mb-2">
                    <label>Select Company *</label>
                    <select v-model="form.company_id" class="form-control">
                        <option value="">Select Company</option>
                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.company_name }}</option>
                    </select>
                    <FormError :message="form.errors.company_id" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Vendor Name</label>
                    <input v-model="form.vendor_name" class="form-control" type="text" readonly disabled>
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Quantity</label>
                    <input v-model="form.quantity" class="form-control" type="text" required placeholder="Enter quantity" @keyup="recalcPrice">
                    <FormError :message="form.errors.quantity" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Price</label>
                    <input v-model="form.price" class="form-control" type="text" required placeholder="Enter price" @keyup="recalcPrice">
                    <FormError :message="form.errors.price" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Discount Amount</label>
                    <input v-model="form.discount_amount" class="form-control" type="number" step="any" min="0" placeholder="Discount Amount" @keyup="applyDiscountAmount">
                    <FormError :message="form.errors.discount_amount" />
                    <span class="text-danger">{{ discountAmountError }}</span>
                </div>
                <span v-if="priceQtyError" class="text-danger h6">{{ priceQtyError }}</span>
                <div class="col-sm-4 mb-2">
                    <label>Discount Percentage %</label>
                    <input v-model="form.discount_percentage" class="form-control" type="text" placeholder="Discount Percentage %" @keyup="applyDiscountPercentage">
                    <FormError :message="form.errors.discount_percentage" />
                    <span class="text-danger">{{ discountPercentageError }}</span>
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Total Price</label>
                    <input v-model="form.total_price" class="form-control" type="text" placeholder="Total price">
                    <FormError :message="form.errors.total_price" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Bilty Number</label>
                    <input v-model="form.bilty_number" class="form-control" type="text" placeholder="ENter Bilty Number">
                    <FormError :message="form.errors.bilty_number" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Bilty Charges</label>
                    <input v-model="form.bilty_charges" class="form-control" type="number" step="any" min="0" placeholder="ENter Bilty Charges">
                    <FormError :message="form.errors.bilty_charges" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Per Bag Discount</label>
                    <input v-model="form.per_bag_discount" class="form-control" type="number" step="any" min="0" placeholder="Enter Bilty Charges">
                    <FormError :message="form.errors.per_bag_discount" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Sale Order Number</label>
                    <input v-model="form.sale_order_number" class="form-control" type="text" placeholder="Enter Sale Order Number">
                    <FormError :message="form.errors.sale_order_number" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Delivery Order Number</label>
                    <input v-model="form.delivery_order_number" class="form-control" type="text" placeholder="Enter Delivery Order Number">
                    <FormError :message="form.errors.delivery_order_number" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Description</label>
                    <input v-model="form.description" class="form-control" type="text" placeholder="description if any">
                    <FormError :message="form.errors.description" />
                </div>
                <div class="col-sm-4 mb-2">
                    <label>Image</label>
                    <input class="form-control" type="file" accept="image/png,image/jpg,image/jpeg" @change="form.image_file = $event.target.files[0] ?? null">
                    <FormError :message="form.errors.image_file" />
                </div>
            </div>
            <div class="row form-group">
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
