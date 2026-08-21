<script setup>
import { computed, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import LocationSelect from '../Forms/LocationSelect.vue';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    party: { type: Object, default: null },
    mode: { type: String, default: 'party' },
    formTitle: { type: String, default: 'Create Party' },
    countries: { type: Array, default: () => [] },
    divisions: { type: Array, default: () => [] },
    customerTypes: { type: Array, default: () => [] },
    farmTypes: { type: Array, default: () => [] },
    farmSubtypes: { type: Array, default: () => [] },
    vendorTypes: { type: Array, default: () => [] },
    businessTypes: { type: Array, default: () => [] },
    contactPersons: { type: Array, default: () => [] },
    amountTypes: { type: Array, default: () => [] },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const route = useRoute();
const page = usePage();
const isCreate = computed(() => props.party === null);
const lockCustomer = computed(() => props.mode === 'customer');
const lockVendor = computed(() => props.mode === 'vendor');

const divisions = ref([...(props.divisions ?? [])]);
const customerTypes = ref([...(props.customerTypes ?? [])]);
const farmTypes = ref([...(props.farmTypes ?? [])]);
const farmSubtypes = ref([...(props.farmSubtypes ?? [])]);
const vendorTypes = ref([...(props.vendorTypes ?? [])]);
const businessTypes = ref([...(props.businessTypes ?? [])]);

watch(() => props.divisions, (value) => { divisions.value = [...(value ?? [])]; });
watch(() => props.customerTypes, (value) => { customerTypes.value = [...(value ?? [])]; });
watch(() => props.farmTypes, (value) => { farmTypes.value = [...(value ?? [])]; });
watch(() => props.farmSubtypes, (value) => { farmSubtypes.value = [...(value ?? [])]; });
watch(() => props.vendorTypes, (value) => { vendorTypes.value = [...(value ?? [])]; });
watch(() => props.businessTypes, (value) => { businessTypes.value = [...(value ?? [])]; });

const lookupLists = {
    divisions,
    customer_types: customerTypes,
    farm_types: farmTypes,
    farm_subtypes: farmSubtypes,
    vendor_types: vendorTypes,
    business_types: businessTypes,
};

const lookupForm = useForm({
    tag_name: '',
    name: '',
});
const lookupOpen = ref(false);
const lookupSelectField = ref('');

const form = useForm({
    is_customer: lockCustomer.value || Number(props.party?.is_customer) === 1 ? 1 : 0,
    is_vendor: lockVendor.value || Number(props.party?.is_vendor) === 1 ? 1 : 0,
    name: props.party?.name ?? '',
    guardian_name: props.party?.guardian_name ?? '',
    cnic_no: props.party?.cnic_no ?? '',
    email: props.party?.email ?? '',
    contact_no: props.party?.contact_no ?? '',
    business_no: props.party?.business_no ?? '',
    manual_number: props.party?.manual_number ?? '',
    address: props.party?.address ?? '',
    description: props.party?.description ?? '',
    country_id: props.party?.country_id ?? '',
    province_id: props.party?.province_id ?? '',
    city_id: props.party?.city_id ?? '',
    contact_person_id: props.party?.contact_person_id ?? '',
    customer_type_id: props.party?.customer_type_id ?? '',
    customer_division_id: props.party?.customer_division_id ?? '',
    farm_type_id: props.party?.farm_type_id ?? '',
    farm_subtype_id: props.party?.farm_subtype_id ?? '',
    farm_name: props.party?.farm_name ?? '',
    farm_noc: props.party?.farm_noc ?? '',
    farm_address: props.party?.farm_address ?? '',
    vendor_type_id: props.party?.vendor_type_id ?? '',
    vendor_division_id: props.party?.vendor_division_id ?? '',
    company_name: props.party?.company_name ?? '',
    business_type_id: props.party?.business_type_id ?? '',
    company_address: props.party?.company_address ?? '',
    opening_balance: props.party?.opening_balance ?? '',
    balance_type: props.party?.balance_type ?? '',
    profile_picture: null,
    cnic_front: null,
    cnic_back: null,
    signature_image: null,
    farm_image: null,
    company_logo: null,
});

const showCustomer = computed(() => Number(form.is_customer) === 1);
const showVendor = computed(() => Number(form.is_vendor) === 1);

function openLookup(table, selectField) {
    lookupForm.reset();
    lookupForm.clearErrors();
    lookupForm.tag_name = table;
    lookupSelectField.value = selectField;
    lookupOpen.value = true;
}

function submitLookup() {
    lookupForm.post(route('inertia.lookup-types.store'), {
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
    form.transform((data) => ({
        ...data,
        is_customer: Number(data.is_customer) === 1 ? 1 : 0,
        is_vendor: Number(data.is_vendor) === 1 ? 1 : 0,
    })).submit(props.method, props.submitUrl, {
        forceFormData: true,
    });
}

function onFileChange(field, event) {
    form[field] = event.target.files[0] ?? null;
}
</script>

<template>
    <div>
        <div class="row mb-2">
            <div class="col-6 align-self-start">
                <h4>{{ formTitle }}</h4>
            </div>
            <div class="col-6 align-self-end text-end mb-2">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm" title="Click to back">
                    <i class="fa fa-arrow-left"></i>
                    Back
                </Link>
            </div>
        </div>

        <form autocomplete="off" @submit.prevent="submit">
            <div class="row form-group">
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="PartyName">Name *</label>
                    <input id="PartyName" v-model="form.name" class="form-control" type="text" placeholder="Enter name">
                    <FormError :message="form.errors.name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="PartyguardianName">Father Name/ Guardian name</label>
                    <input id="PartyguardianName" v-model="form.guardian_name" class="form-control" type="text" placeholder="Enter Father/Gardian name">
                    <FormError :message="form.errors.guardian_name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="PartyCnicNo">CNIC *</label>
                    <input id="PartyCnicNo" v-model="form.cnic_no" class="form-control" type="text" placeholder="Enter CNIC number">
                    <FormError :message="form.errors.cnic_no" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold">Select Type*</label><br>
                    <div v-if="! lockCustomer" class="form-check mb-2 mt-1 form-check-inline">
                        <input
                            id="VendorCheckBox"
                            class="form-check-input"
                            type="checkbox"
                            :checked="Number(form.is_vendor) === 1"
                            :disabled="lockVendor"
                            @change="form.is_vendor = $event.target.checked ? 1 : 0"
                        >
                        <label class="font_bold form-check-label" style="padding: 5px;" for="VendorCheckBox">Vendor</label>
                    </div>
                    <div v-if="! lockVendor" class="form-check mb-2 form-check-inline form-check-success">
                        <input
                            id="CustomerCheckBox"
                            class="form-check-input"
                            type="checkbox"
                            :checked="Number(form.is_customer) === 1"
                            :disabled="lockCustomer"
                            @change="form.is_customer = $event.target.checked ? 1 : 0"
                        >
                        <label class="font_bold form-check-label" style="padding: 5px;" for="CustomerCheckBox">Customer</label>
                    </div>
                    <FormError :message="form.errors.is_customer || form.errors.is_vendor" />
                    <small v-if="! showCustomer && ! showVendor" class="text-muted">Tick Vendor or Customer to show extra fields.</small>
                </div>

                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="customerEmail">Email</label>
                    <input id="customerEmail" v-model="form.email" class="form-control" type="text" placeholder="Enter email">
                    <FormError :message="form.errors.email" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="PartyContactNo">Contact Number *</label>
                    <input id="PartyContactNo" v-model="form.contact_no" class="form-control" type="number" placeholder="Enter contact number">
                    <FormError :message="form.errors.contact_no" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="font_bold" for="PartyBusinessNo">Business Number</label>
                    <input id="PartyBusinessNo" v-model="form.business_no" class="form-control" type="number" placeholder="Enter business number">
                    <FormError :message="form.errors.business_no" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="PartyManualNumber">Manual Series Number</label>
                    <input id="PartyManualNumber" v-model="form.manual_number" class="form-control" type="text" placeholder="Enter Manual Series Number">
                    <FormError :message="form.errors.manual_number" />
                </div>
            </div>

            <div class="row mt-1">
                <LocationSelect
                    :countries="countries"
                    :country-id="form.country_id"
                    :province-id="form.province_id"
                    :city-id="form.city_id"
                    :errors="form.errors"
                    @update:country-id="form.country_id = $event"
                    @update:province-id="form.province_id = $event"
                    @update:city-id="form.city_id = $event"
                />
                <div class="col-3 mb-2">
                    <label class="font_bold" for="PartyAddress">Address</label>
                    <input id="PartyAddress" v-model="form.address" class="form-control" type="text" placeholder="Enter Address">
                    <FormError :message="form.errors.address" />
                </div>
            </div>

            <div v-if="showCustomer" id="CustomerFarmRow" class="row border border-2 border-dark rounded-2 mt-1">
                <h4 class="my-2">Customer Data</h4>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="CustomerDivision">Select Division</label>
                    <div class="input-group">
                        <select id="CustomerDivision" v-model="form.customer_division_id" class="form-control">
                            <option value="">Select division</option>
                            <option v-for="item in divisions" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('divisions', 'customer_division_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.customer_division_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="PartyCustomerType">Select Customer Type*</label>
                    <div class="input-group">
                        <select id="PartyCustomerType" v-model="form.customer_type_id" class="form-control">
                            <option value="">Select Customer Type</option>
                            <option v-for="item in customerTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('customer_types', 'customer_type_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.customer_type_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="CustomerFarmType">Select Farm Type *</label>
                    <div class="input-group">
                        <select id="CustomerFarmType" v-model="form.farm_type_id" class="form-control">
                            <option value="">Select Farm Type</option>
                            <option v-for="item in farmTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('farm_types', 'farm_type_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.farm_type_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="PartyFarmSubtype">Select Farm Subtype *</label>
                    <div class="input-group">
                        <select id="PartyFarmSubtype" v-model="form.farm_subtype_id" class="form-control">
                            <option value="">Select Farm subtype</option>
                            <option v-for="item in farmSubtypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('farm_subtypes', 'farm_subtype_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.farm_subtype_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="customerFarmName">Farm Name *</label>
                    <input id="customerFarmName" v-model="form.farm_name" class="form-control" type="text" placeholder="Enter Farm Name">
                    <FormError :message="form.errors.farm_name" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="FarmNOC">Farm NOC *</label>
                    <input id="FarmNOC" v-model="form.farm_noc" class="form-control" type="text" placeholder="Enter Farm NOC number">
                    <FormError :message="form.errors.farm_noc" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="FarmImage">Farm Image *</label>
                    <input id="FarmImage" class="form-control" type="file" @change="onFileChange('farm_image', $event)">
                    <small v-if="! isCreate && party?.farm_image" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.farm_image" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="CustomerFarmAddress">Farm Address*</label>
                    <input id="CustomerFarmAddress" v-model="form.farm_address" class="form-control" type="text" placeholder="Enter Farm Address">
                    <FormError :message="form.errors.farm_address" />
                </div>
            </div>

            <div v-if="showVendor" id="VendorCompanyRow" class="row border border-2 rounded-2 border-primary mt-2">
                <h4 class="my-2">Company data</h4>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="PartyVendorDivision">Select Division*</label>
                    <div class="input-group">
                        <select id="PartyVendorDivision" v-model="form.vendor_division_id" class="form-control">
                            <option value="">Select division</option>
                            <option v-for="item in divisions" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('divisions', 'vendor_division_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.vendor_division_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="PartyVendorType">Select Vendor Type *</label>
                    <div class="input-group">
                        <select id="PartyVendorType" v-model="form.vendor_type_id" class="form-control">
                            <option value="">Select Vendor Type</option>
                            <option v-for="item in vendorTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('vendor_types', 'vendor_type_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.vendor_type_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="PartyCompanyName">Company Name*</label>
                    <input id="PartyCompanyName" v-model="form.company_name" class="form-control" type="text" placeholder="Enter Company Name">
                    <FormError :message="form.errors.company_name" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="PartyBusinessType">Select Business Type *</label>
                    <div class="input-group">
                        <select id="PartyBusinessType" v-model="form.business_type_id" class="form-control">
                            <option value="">Select Business Type</option>
                            <option v-for="item in businessTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark waves-effect waves-light" title="Click to add new" @click.prevent="openLookup('business_types', 'business_type_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.business_type_id" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="CompanyLogo">Company logo *</label>
                    <input id="CompanyLogo" class="form-control" type="file" @change="onFileChange('company_logo', $event)">
                    <small v-if="! isCreate && party?.company_logo" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.company_logo" />
                </div>
                <div class="col-4 mb-2">
                    <label class="font_bold" for="CompanyAddress">Company Address *</label>
                    <input id="CompanyAddress" v-model="form.company_address" class="form-control" type="text" placeholder="Enter Company Address">
                    <FormError :message="form.errors.company_address" />
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-3 mb-2">
                    <label class="font_bold" for="PartyProfileImage">Profile Picture</label>
                    <input id="PartyProfileImage" class="form-control" type="file" @change="onFileChange('profile_picture', $event)">
                    <FormError :message="form.errors.profile_picture" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="CnicFront">Cnic Front*</label>
                    <input id="CnicFront" class="form-control" type="file" @change="onFileChange('cnic_front', $event)">
                    <small v-if="! isCreate && party?.cnic_front" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.cnic_front" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="CnicBack">Cnic Back*</label>
                    <input id="CnicBack" class="form-control" type="file" @change="onFileChange('cnic_back', $event)">
                    <small v-if="! isCreate && party?.cnic_back" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.cnic_back" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="PartySignatureImage">Signature</label>
                    <input id="PartySignatureImage" class="form-control" type="file" @change="onFileChange('signature_image', $event)">
                    <FormError :message="form.errors.signature_image" />
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-3 mb-2">
                    <label class="font_bold" for="PartyContactPerson">Select contact Person</label>
                    <select id="PartyContactPerson" v-model="form.contact_person_id" class="form-control">
                        <option value="">Select contact Person</option>
                        <option v-for="item in contactPersons" :key="item.id" :value="item.id">{{ item.name }}</option>
                    </select>
                    <FormError :message="form.errors.contact_person_id" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="Balance">Balance</label>
                    <input id="Balance" v-model="form.opening_balance" class="form-control" type="number" min="0" step="any" placeholder="Enter Balance">
                    <FormError :message="form.errors.opening_balance" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold">Balance Type</label>
                    <select v-model="form.balance_type" class="form-control">
                        <option value="">Select type</option>
                        <option v-for="item in amountTypes" :key="item.value" :value="item.value">{{ item.label }}</option>
                    </select>
                    <FormError :message="form.errors.balance_type" />
                </div>
                <div class="col-3">
                    <label class="font_bold" for="Description">Description</label>
                    <textarea id="Description" v-model="form.description" class="form-control" rows="2" placeholder="Description"></textarea>
                    <FormError :message="form.errors.description" />
                </div>
            </div>

            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                    <Link :href="indexUrl" class="ms-1">
                        <SecondaryButton type="button">Cancel</SecondaryButton>
                    </Link>
                </div>
            </div>
        </form>

        <FormModal :show="lookupOpen" title="Add new" size="md" @close="lookupOpen = false">
            <form autocomplete="off" @submit.prevent="submitLookup">
                <div class="row form-group">
                    <div class="col-sm-12 mb-2">
                        <label for="TypeName">Name *</label>
                        <input id="TypeName" v-model="lookupForm.name" class="form-control" type="text" placeholder="Enter name">
                        <FormError :message="lookupForm.errors.name" />
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-4 mb-3">
                        <PrimaryButton type="submit" :disabled="lookupForm.processing">Submit</PrimaryButton>
                        <SecondaryButton type="button" class="ms-1" @click="lookupOpen = false">Cancel</SecondaryButton>
                    </div>
                </div>
            </form>
        </FormModal>
    </div>
</template>
