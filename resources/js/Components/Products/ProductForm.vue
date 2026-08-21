<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    product: { type: Object, default: null },
    formTitle: { type: String, default: 'Add Product Entry' },
    productGroups: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    productCategories: { type: Array, default: () => [] },
    productTypes: { type: Array, default: () => [] },
    productStores: { type: Array, default: () => [] },
    vaccinationGroups: { type: Array, default: () => [] },
    packSizeUnitTypes: { type: Array, default: () => [] },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const route = useRoute();
const page = usePage();
const isCreate = computed(() => props.product === null);
const productCategories = ref([...(props.productCategories ?? [])]);
const vaccinationGroups = ref([...(props.vaccinationGroups ?? [])]);

watch(() => props.productCategories, (value) => { productCategories.value = [...(value ?? [])]; });
watch(() => props.vaccinationGroups, (value) => { vaccinationGroups.value = [...(value ?? [])]; });

const lookupLists = {
    product_categories: productCategories,
    vaccination_groups: vaccinationGroups,
};
const lookupForm = useForm({ tag_name: '', name: '' });
const lookupOpen = ref(false);
const lookupSelectField = ref('');

const form = useForm({
    product_group: props.product?.product_group ?? '',
    company_id: props.product?.company_id ?? '',
    product_category_id: props.product?.product_category_id ?? '',
    product_name: props.product?.product_name ?? '',
    batch_number: props.product?.batch_number ?? '',
    serial_number: props.product?.serial_number ?? '',
    product_type: props.product?.product_type ?? '',
    vaccination_group: props.product?.vaccination_group ?? '',
    pack_size_unit: props.product?.pack_size_unit ?? '',
    pack_size_unit_type: props.product?.pack_size_unit_type ?? '',
    store_id: props.product?.store_id ?? '',
    rack_number: props.product?.rack_number ?? '',
    min_level: props.product?.min_level ?? '',
    max_level: props.product?.max_level ?? '',
    mrp_price: props.product?.mrp_price ?? '',
    whole_sale_price: props.product?.whole_sale_price ?? '',
    full_less_price: props.product?.full_less_price ?? '',
    store_price: props.product?.store_price ?? '',
    retail_price: props.product?.retail_price ?? '',
    trade_price: props.product?.trade_price ?? '',
    purchase_price: props.product?.purchase_price ?? '',
    sale_price: props.product?.sale_price ?? '',
    discount_amount: props.product?.discount_amount ?? '',
    tax_percentage: props.product?.tax_percentage ?? '',
    tax_amount: props.product?.tax_amount ?? '',
    discount_percentage: props.product?.discount_percentage ?? '',
    warranty_period: props.product?.warranty_period ?? '',
    is_taxable: props.product?.is_taxable ? 1 : 0,
    is_sale_on_tp: props.product?.is_sale_on_tp ? 1 : 0,
    is_claimable: props.product?.is_claimable ? 1 : 0,
    is_fridged: props.product?.is_fridged ? 1 : 0,
    is_narcotic: props.product?.is_narcotic ? 1 : 0,
    is_unwaranted: props.product?.is_unwaranted ? 1 : 0,
    description: props.product?.description ?? '',
    product_picture: null,
});

function openLookup(table, selectField) {
    lookupForm.reset();
    lookupForm.clearErrors();
    lookupForm.tag_name = table;
    lookupSelectField.value = selectField;
    lookupOpen.value = true;
}

function submitLookup() {
    lookupForm.post(route('inertia.farm-lookup-types.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const created = page.props.flash?.createdLookup;
            if (created?.id) {
                const list = lookupLists[created.table];
                if (list && ! list.value.some((item) => Number(item.id) === Number(created.id))) {
                    list.value = [...list.value, { id: created.id, name: created.name }];
                }
                if (lookupSelectField.value) {
                    form[lookupSelectField.value] = created.id;
                }
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
            <div class="col-6"><h4>{{ formTitle }}</h4></div>
            <div class="col-6 text-end">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</Link>
            </div>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row">
                <div class="col-md-4 border border-2">
                    <div class="row mt-2">
                        <div class="col-12 mb-2">
                            <label class="fw-bold">Select Product Group*</label>
                            <select v-model="form.product_group" class="form-control">
                                <option value="">Select Group</option>
                                <option v-for="item in productGroups" :key="item.value" :value="item.value">{{ item.label }}</option>
                            </select>
                            <FormError :message="form.errors.product_group" />
                        </div>
                        <div class="col-12 mb-2">
                            <label class="font_bold">Select Company*</label>
                            <select v-model="form.company_id" class="form-control">
                                <option value="">Select company</option>
                                <option v-for="item in companies" :key="item.id" :value="item.id">{{ item.company_name }}</option>
                            </select>
                            <FormError :message="form.errors.company_id" />
                        </div>
                        <div class="col-12 mb-2">
                            <label class="font_bold">Select Product Category*</label>
                            <div class="input-group">
                                <select v-model="form.product_category_id" class="form-control">
                                    <option value="">Select category</option>
                                    <option v-for="item in productCategories" :key="item.id" :value="item.id">{{ item.name }}</option>
                                </select>
                                <a href="#" class="btn input-group-text btn-dark" @click.prevent="openLookup('product_categories', 'product_category_id')">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                            <FormError :message="form.errors.product_category_id" />
                        </div>
                        <div class="col-12 mb-2">
                            <label class="font_bold">Product Name*</label>
                            <input v-model="form.product_name" class="form-control" type="text" placeholder="Product Name">
                            <FormError :message="form.errors.product_name" />
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font_bold">Batch Number</label>
                            <input v-model="form.batch_number" class="form-control" type="text">
                            <FormError :message="form.errors.batch_number" />
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font_bold">Serial Number</label>
                            <input v-model="form.serial_number" class="form-control" type="text">
                            <FormError :message="form.errors.serial_number" />
                        </div>
                        <div class="col-12 mb-2">
                            <label class="font_bold">Product Type*</label>
                            <select v-model="form.product_type" class="form-control">
                                <option value="">Select Type</option>
                                <option v-for="item in productTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <FormError :message="form.errors.product_type" />
                        </div>
                        <div class="col-12 mb-2">
                            <label class="font_bold">Vaccination Group</label>
                            <div class="input-group">
                                <select v-model="form.vaccination_group" class="form-control">
                                    <option value="">Select Group</option>
                                    <option v-for="item in vaccinationGroups" :key="item.id" :value="item.id">{{ item.name }}</option>
                                </select>
                                <a href="#" class="btn input-group-text btn-dark" @click.prevent="openLookup('vaccination_groups', 'vaccination_group')">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                            <FormError :message="form.errors.vaccination_group" />
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font_bold">Pack Size(units)</label>
                            <input v-model="form.pack_size_unit" class="form-control" type="number" min="0" step="any">
                            <FormError :message="form.errors.pack_size_unit" />
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="font_bold">Select Unit type</label>
                            <select v-model="form.pack_size_unit_type" class="form-control">
                                <option value="">Select Type</option>
                                <option v-for="item in packSizeUnitTypes" :key="item.value" :value="item.value">{{ item.label }}</option>
                            </select>
                            <FormError :message="form.errors.pack_size_unit_type" />
                        </div>
                    </div>
                </div>
                <div class="col-md-8 border border-2">
                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            <label class="font_bold">Select Store*</label>
                            <select v-model="form.store_id" class="form-control">
                                <option value="">Select Store</option>
                                <option v-for="item in productStores" :key="item.id" :value="item.id">{{ item.store_name }}</option>
                            </select>
                            <FormError :message="form.errors.store_id" />
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="font_bold">Rack Number</label>
                            <input v-model="form.rack_number" class="form-control" type="number" min="0">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="font_bold">Inventory Level</label>
                            <div class="row">
                                <div class="col-6"><input v-model="form.min_level" class="form-control" type="number" min="0" placeholder="Min level"></div>
                                <div class="col-6"><input v-model="form.max_level" class="form-control" type="number" min="0" placeholder="Max level"></div>
                            </div>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">MRP Price</label>
                            <input v-model="form.mrp_price" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Whole Sale Price</label>
                            <input v-model="form.whole_sale_price" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Full Less Price</label>
                            <input v-model="form.full_less_price" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Store Price</label>
                            <input v-model="form.store_price" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Retail Price</label>
                            <input v-model="form.retail_price" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Trade Price</label>
                            <input v-model="form.trade_price" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Purchase Price*</label>
                            <input v-model="form.purchase_price" class="form-control" type="number" min="0" step="any">
                            <FormError :message="form.errors.purchase_price" />
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Sale Price*</label>
                            <input v-model="form.sale_price" class="form-control" type="number" min="0" step="any">
                            <FormError :message="form.errors.sale_price" />
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Discount Amount</label>
                            <input v-model="form.discount_amount" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Tax %</label>
                            <input v-model="form.tax_percentage" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Tax Amount</label>
                            <input v-model="form.tax_amount" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Discount Percentage %</label>
                            <input v-model="form.discount_percentage" class="form-control" type="number" min="0" step="any">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Warranty Period</label>
                            <input v-model="form.warranty_period" class="form-control" type="text">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="font_bold">Product Picture</label>
                            <input class="form-control" type="file" @change="form.product_picture = $event.target.files[0] ?? null">
                            <small v-if="! isCreate && product?.product_picture" class="text-muted">Leave blank to keep the current file.</small>
                            <FormError :message="form.errors.product_picture" />
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-check-label me-3"><input v-model="form.is_taxable" class="form-check-input me-1" type="checkbox" :true-value="1" :false-value="0"> Is Tax</label>
                            <label class="form-check-label me-3"><input v-model="form.is_sale_on_tp" class="form-check-input me-1" type="checkbox" :true-value="1" :false-value="0"> Is sale on TP</label>
                            <label class="form-check-label me-3"><input v-model="form.is_claimable" class="form-check-input me-1" type="checkbox" :true-value="1" :false-value="0"> Is Clamable</label>
                            <label class="form-check-label me-3"><input v-model="form.is_fridged" class="form-check-input me-1" type="checkbox" :true-value="1" :false-value="0"> Is Fridge</label>
                            <label class="form-check-label me-3"><input v-model="form.is_narcotic" class="form-check-input me-1" type="checkbox" :true-value="1" :false-value="0"> Is Narcotic</label>
                            <label class="form-check-label me-3"><input v-model="form.is_unwaranted" class="form-check-input me-1" type="checkbox" :true-value="1" :false-value="0"> Is Unwarranted</label>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="font_bold">Description</label>
                            <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                <Link :href="indexUrl" class="ms-1"><SecondaryButton type="button">Cancel</SecondaryButton></Link>
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
