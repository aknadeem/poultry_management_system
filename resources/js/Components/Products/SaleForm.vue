<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';

const props = defineProps({
    formTitle: { type: String, default: 'Add Sale Entry' },
    divisions: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    productCategories: { type: Array, default: () => [] },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
});

const page = usePage();
const catalog = ref([]);
const addOpen = ref(false);
const companyError = ref('');
const categoryError = ref('');
const discountError = ref('');
const lineError = ref('');
const selectedProductId = ref('');

const emptyDraft = () => ({
    product_id: '',
    product_code: '',
    product_name: '',
    product_sale_price: '',
    product_qty: '',
    product_bonus_qty: 0,
    product_total_qty: '',
    product_discount: 0,
    product_discount_percentage: 0,
    product_total_price: '',
    final_price: '',
});

const draft = ref(emptyDraft());

const form = useForm({
    division_id: '',
    party_id: '',
    party_company_id: '',
    product_category_id: '',
    sale_date: new Date().toISOString().slice(0, 10),
    due_date_option: '',
    manual_number: '',
    sale_type: 'cash',
    total_amount: '',
    discount_amount: 0,
    discount_percentage: 0,
    other_charges: 0,
    final_amount: '',
    invoice_picture: null,
    description: '',
    product_id: [],
    product_code: [],
    product_name: [],
    product_sale_price: [],
    product_qty: [],
    product_bonus_qty: [],
    product_total_qty: [],
    product_discount: [],
    product_discount_percentage: [],
    product_total_price: [],
});

const items = ref([]);

const filteredCustomers = computed(() => {
    const divisionId = Number(form.division_id) || 0;
    if (divisionId < 1) {
        return [];
    }

    return props.customers.filter((customer) => Number(customer.customer_division_id) === divisionId);
});

const draftQty = computed(() => Number(draft.value.product_qty) || 0);
const draftPrice = computed(() => Number(draft.value.product_sale_price) || 0);
const draftBonus = computed(() => Number(draft.value.product_bonus_qty) || 0);
const draftDiscount = computed(() => Number(draft.value.product_discount) || 0);
const draftDiscountPercent = computed(() => Number(draft.value.product_discount_percentage) || 0);

function recalcDraft() {
    const qty = draftQty.value;
    const price = draftPrice.value;
    const bonus = draftBonus.value;
    const subtotal = qty > 0 && price > 0 ? qty * price : 0;
    let discount = draftDiscount.value;
    let percent = draftDiscountPercent.value;

    if (percent > 0 && percent < 100 && subtotal > 0 && discount <= 0) {
        discount = Number(((percent / 100) * subtotal).toFixed(2));
    } else if (discount > 0 && subtotal > 0) {
        percent = Number(((discount / subtotal) * 100).toFixed(2));
    }

    draft.value.product_total_qty = qty + bonus;
    draft.value.product_total_price = subtotal || '';
    draft.value.final_price = subtotal > 0 ? Math.max(subtotal - discount, 0) : '';
}

watch(
    () => [
        draft.value.product_qty,
        draft.value.product_sale_price,
        draft.value.product_bonus_qty,
        draft.value.product_discount,
        draft.value.product_discount_percentage,
    ],
    recalcDraft,
);

watch(() => form.division_id, () => {
    form.party_id = '';
});

watch([() => form.party_company_id, () => form.product_category_id], () => {
    items.value = [];
    form.total_amount = '';
    form.discount_amount = 0;
    form.other_charges = 0;
    form.final_amount = '';
    companyError.value = '';
    categoryError.value = '';
});

watch(items, () => {
    syncItemArrays();
    recalculateTotals();
}, { deep: true });

watch([() => form.discount_amount, () => form.other_charges], () => {
    recalculateTotals();
});

function syncItemArrays() {
    form.product_id = items.value.map((item) => item.product_id);
    form.product_code = items.value.map((item) => item.product_code);
    form.product_name = items.value.map((item) => item.product_name);
    form.product_sale_price = items.value.map((item) => item.product_sale_price);
    form.product_qty = items.value.map((item) => item.product_qty);
    form.product_bonus_qty = items.value.map((item) => item.product_bonus_qty);
    form.product_total_qty = items.value.map((item) => item.product_total_qty);
    form.product_discount = items.value.map((item) => item.product_discount);
    form.product_discount_percentage = items.value.map((item) => item.product_discount_percentage);
    form.product_total_price = items.value.map((item) => item.product_total_price);
}

function recalculateTotals() {
    const total = items.value.reduce((sum, item) => sum + Number(item.product_total_price || 0), 0);
    const discount = Number(form.discount_amount) || 0;
    const charges = Number(form.other_charges) || 0;

    form.total_amount = items.value.length ? total : '';
    if (discount > 0 && total > 0 && discount >= total) {
        discountError.value = 'Discount Amount Shoud be Less then Total Amount';
        form.final_amount = total + charges;

        return;
    }

    discountError.value = '';
    form.final_amount = items.value.length ? (total - discount) + charges : '';
}

async function openAddProduct() {
    const companyId = Number(form.party_company_id) || 0;
    const categoryId = Number(form.product_category_id) || 0;
    companyError.value = companyId < 1 ? 'Company is required to add a product' : '';
    categoryError.value = categoryId < 1 ? 'Category is required to add a product' : '';

    if (companyId < 1 || categoryId < 1) {
        return;
    }
    const base = page.props.routes.productfilter;
    const response = await fetch(`${base}/${companyId}/cat/${categoryId}`, { credentials: 'same-origin' });
    const payload = await response.json();
    catalog.value = payload.data ?? [];
    selectedProductId.value = '';
    draft.value = emptyDraft();
    lineError.value = '';
    addOpen.value = true;
}

function onSelectProduct(event) {
    selectedProductId.value = event.target.value;
    const product = catalog.value.find((item) => Number(item.id) === Number(event.target.value));
    if (! product) {
        return;
    }

    draft.value.product_id = product.id;
    draft.value.product_code = product.product_code;
    draft.value.product_name = product.product_name;
    draft.value.product_sale_price = product.sale_price;
    draft.value.product_discount = product.discount_amount ?? 0;
    draft.value.product_discount_percentage = product.discount_percentage ?? 0;
    recalcDraft();
}

function addLine() {
    recalcDraft();
    if (! draft.value.product_id || draftQty.value < 1 || Number(draft.value.final_price) <= 0) {
        lineError.value = 'Quantity is Required, Please Select Quantity';

        return;
    }

    items.value.push({
        product_id: draft.value.product_id,
        product_code: draft.value.product_code,
        product_name: draft.value.product_name,
        product_sale_price: draft.value.product_sale_price,
        product_qty: draftQty.value,
        product_bonus_qty: draftBonus.value,
        product_total_qty: Number(draft.value.product_total_qty) || draftQty.value,
        product_discount: Math.max((Number(draft.value.product_total_price) || 0) - Number(draft.value.final_price), 0),
        product_discount_percentage: draftDiscountPercent.value,
        product_total_price: Number(draft.value.final_price),
    });
    addOpen.value = false;
}

function removeLine(index) {
    items.value.splice(index, 1);
}

function submit() {
    syncItemArrays();
    recalculateTotals();
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
                <Link :href="indexUrl" class="btn btn-secondary btn-sm" title="Click to go back">
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form id="ProductSaleForm" autocomplete="off" @submit.prevent="submit">
            <div class="row">
                <div class="col-3 border border-2">
                    <div class="row mt-2">
                        <div class="col-12 mb-3">
                            <label class="font_bold">Select Divisions</label>
                            <select v-model="form.division_id" class="form-control" required>
                                <option value="">Select division</option>
                                <option v-for="item in divisions" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <FormError :message="form.errors.division_id" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Select Customer*</label>
                            <select v-model="form.party_id" class="form-control" required :disabled="! form.division_id">
                                <option value="">Select customer</option>
                                <option v-for="item in filteredCustomers" :key="item.id" :value="item.id">
                                    {{ item.name }} [{{ item.cnic_no }}]
                                </option>
                            </select>
                            <FormError :message="form.errors.party_id" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Select Company*</label>
                            <select v-model="form.party_company_id" class="form-control" required>
                                <option value="">Select company</option>
                                <option v-for="item in companies" :key="item.id" :value="item.id">{{ item.company_name }}</option>
                            </select>
                            <FormError :message="form.errors.party_company_id" />
                            <span class="text-danger">{{ companyError }}</span>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Select Category*</label>
                            <select v-model="form.product_category_id" class="form-control" required>
                                <option value="">Select category</option>
                                <option v-for="item in productCategories" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <FormError :message="form.errors.product_category_id" />
                            <span class="text-danger">{{ categoryError }}</span>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Sale Date*</label>
                            <input v-model="form.sale_date" class="form-control" type="date" required>
                            <FormError :message="form.errors.sale_date" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Due Date*</label>
                            <input v-model="form.due_date_option" class="form-control" type="text" required>
                            <FormError :message="form.errors.due_date_option" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Manual Number</label>
                            <input v-model="form.manual_number" class="form-control" type="text" placeholder="Manual Number">
                            <FormError :message="form.errors.manual_number" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font_bold">Sale Type *</label>
                            <div class="mt-1">
                                <div class="form-check-inline">
                                    <input id="cashRadio" v-model="form.sale_type" class="form-check-input rounded-0" type="radio" value="cash" required>
                                    <label class="form-check-label font_bold" for="cashRadio">Cash</label>
                                    &nbsp;&nbsp;
                                    <input id="creditRadio" v-model="form.sale_type" class="form-check-input rounded-0" type="radio" value="credit" required>
                                    <label class="form-check-label font_bold" for="creditRadio">Credit</label>
                                </div>
                            </div>
                            <FormError :message="form.errors.sale_type" />
                        </div>
                    </div>
                </div>
                <div class="col-9 border border-2">
                    <table class="table table-border">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Bonus-Qty</th>
                                <th>Discount</th>
                                <th>Total Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.product_id + '-' + index">
                                <td class="px-1" style="width:40%">
                                    <input class="form-control" type="text" :value="item.product_name" readonly>
                                </td>
                                <td class="px-1">
                                    <input class="form-control" type="number" :value="item.product_sale_price" readonly>
                                </td>
                                <td class="px-1" style="width:8%">
                                    <input class="form-control" type="number" :value="item.product_qty" readonly>
                                </td>
                                <td class="px-1" style="width:12%">
                                    <input class="form-control" type="number" :value="item.product_bonus_qty" readonly>
                                </td>
                                <td class="px-1">
                                    <input class="form-control" type="number" :value="item.product_discount" readonly>
                                </td>
                                <td class="px-1">
                                    <input class="form-control" type="number" :value="item.product_total_price" readonly>
                                </td>
                                <td class="px-1 text-end" style="width:1%">
                                    <span class="btn btn-danger btn-xs text-white" title="Click to Remove">
                                        <button type="button" class="btn-close btn-danger text-danger mt-1" title="Click to Remove" @click="removeLine(index)"></button>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="1" class="text-start">
                                    <div class="card card-body" style="background: #eee;">
                                        <span class="btn btn-secondary" @click="openAddProduct">
                                            <i class="fa fa-plus"></i> Add Item
                                        </span>
                                    </div>
                                    <FormError :message="form.errors.items" />
                                </td>
                                <td colspan="6" class="text-end">
                                    <label class="pt-1"><b>Total Amount: &nbsp;</b></label>
                                    <input v-model="form.total_amount" class="form-control" type="number" readonly placeholder="Total Amount" style="width:35%; float:right;">
                                    <br><br>
                                    <label class="pt-1"><b>Add Discount: &nbsp;</b></label>
                                    <input v-model="form.discount_amount" class="form-control" type="number" min="0" step="any" placeholder="Add Discount" style="width:35%; float:right;">
                                    <span class="text-danger">{{ discountError }}</span>
                                    <br><br>
                                    <label class="pt-1"><b>Other Charges*: &nbsp;</b></label>
                                    <input v-model="form.other_charges" class="form-control" type="number" min="0" step="any" placeholder="Other charges" required style="width:35%; float:right;">
                                    <FormError :message="form.errors.other_charges" />
                                    <br><br>
                                    <label class="pt-1"><b>Final Amount*: &nbsp;</b></label>
                                    <input v-model="form.final_amount" class="form-control" type="number" readonly placeholder="Final amount" required style="width:35%; float:right;">
                                    <FormError :message="form.errors.final_amount" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="row">
                        <div class="col-4 mb-2">
                            <label class="fw-bold fs-5">Invoice Picture</label>
                            <input class="form-control" type="file" @change="form.invoice_picture = $event.target.files[0] ?? null">
                            <FormError :message="form.errors.invoice_picture" />
                        </div>
                        <div class="col-8 mb-2">
                            <label class="fw-bold fs-5">Description</label>
                            <input v-model="form.description" class="form-control" type="text" placeholder="Description">
                            <FormError :message="form.errors.description" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-2 offset-sm-10 text-end">
                    <button type="submit" class="btn btn-secondary AddUpdate" :disabled="form.processing">Submit</button>
                    <Link :href="indexUrl" class="btn btn-danger ModalClosed">Cancel</Link>
                </div>
            </div>
        </form>
        <FormModal :show="addOpen" title="Add Items" size="lg" @close="addOpen = false">
            <form autocomplete="off" @submit.prevent="addLine">
                <div class="row form-group">
                    <div class="col-6 mb-2 px-1">
                        <label class="font_bold">Select Product *</label>
                        <select :value="selectedProductId" class="form-control" required @change="onSelectProduct">
                            <option value="">Select product</option>
                            <option v-for="item in catalog" :key="item.id" :value="item.id">
                                {{ item.product_name }} [{{ item.product_code }}]
                            </option>
                        </select>
                    </div>
                    <div class="col-4 mb-1 px-1">
                        <label class="font_bold">Sale Price</label>
                        <input v-model="draft.product_sale_price" class="form-control" type="number" min="0" step="any" placeholder="Product Price">
                    </div>
                    <div class="col-2 mb-2 px-1">
                        <label class="font_bold">Quantity</label>
                        <input v-model="draft.product_qty" class="form-control" type="number" min="0" placeholder="Quantity" required>
                    </div>
                    <div class="col-2 mb-2 px-1">
                        <label class="font_bold">Bonus Quantity</label>
                        <input v-model="draft.product_bonus_qty" class="form-control" type="number" min="0" placeholder="Bonus Qty">
                    </div>
                    <div class="col-2 mb-2 px-1">
                        <label class="font_bold">Total Quantity</label>
                        <input :value="draft.product_total_qty" class="form-control" type="number" min="0" placeholder="total Qty" readonly>
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold">Total Price</label>
                        <input :value="draft.product_total_price" class="form-control" type="number" min="0" step="any" placeholder="Total Price" readonly>
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold">Discount Amount</label>
                        <input v-model="draft.product_discount" class="form-control" type="number" min="0" step="any" placeholder="Discount Amount">
                    </div>
                    <div class="col-2 mb-2 px-1">
                        <label class="font_bold">Discount %</label>
                        <input v-model="draft.product_discount_percentage" class="form-control" type="number" min="0" step="any" placeholder="Discount %">
                    </div>
                    <div class="col-3 mb-2 px-1">
                        <label class="font_bold">Final Price</label>
                        <input :value="draft.final_price" class="form-control" type="number" min="0" step="any" placeholder="Final price" readonly>
                    </div>
                    <span class="text-danger">{{ lineError }}</span>
                </div>
                <div class="row form-group">
                    <div class="col-sm-4 mb-3">
                        <button type="submit" class="btn btn-secondary btn-sm waves-effect waves-light mt-3">Add</button>
                        <button type="button" class="btn btn-light btn-sm waves-effect waves-light mt-3" @click="addOpen = false">Cancel</button>
                    </div>
                </div>
            </form>
        </FormModal>
    </div>
</template>
