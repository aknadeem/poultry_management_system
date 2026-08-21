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
    employee: { type: Object, default: null },
    formTitle: { type: String, default: 'Create' },
    countries: { type: Array, default: () => [] },
    employeeTypes: { type: Array, default: () => [] },
    employeeLevels: { type: Array, default: () => [] },
    personalFarms: { type: Array, default: () => [] },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const route = useRoute();
const page = usePage();
const isCreate = computed(() => props.employee === null);
const employeeTypes = ref([...(props.employeeTypes ?? [])]);
const employeeLevels = ref([...(props.employeeLevels ?? [])]);

watch(() => props.employeeTypes, (value) => { employeeTypes.value = [...(value ?? [])]; });
watch(() => props.employeeLevels, (value) => { employeeLevels.value = [...(value ?? [])]; });

const lookupLists = {
    employee_types: employeeTypes,
    employee_levels: employeeLevels,
};
const lookupForm = useForm({ tag_name: '', name: '' });
const lookupOpen = ref(false);
const lookupSelectField = ref('');

const form = useForm({
    name: props.employee?.name ?? '',
    guardian_name: props.employee?.guardian_name ?? '',
    contact_no: props.employee?.contact_no ?? '',
    other_number: props.employee?.other_number ?? '',
    email: props.employee?.email ?? '',
    cnic_no: props.employee?.cnic_no ?? '',
    father_cnic_no: props.employee?.father_cnic_no ?? '',
    date_of_birth: props.employee?.date_of_birth ?? '',
    employee_type_id: props.employee?.employee_type_id ?? '',
    employee_level_id: props.employee?.employee_level_id ?? '',
    basic_salary: props.employee?.basic_salary ?? '',
    other_amount: props.employee?.other_amount ?? '',
    net_salary: props.employee?.net_salary ?? '',
    contract_period: props.employee?.contract_period ?? '',
    joining_date: props.employee?.joining_date ?? '',
    personal_farm_id: props.employee?.personal_farm_id ?? '',
    blood_group: props.employee?.blood_group ?? '',
    country_id: props.employee?.country_id ?? '',
    province_id: props.employee?.province_id ?? '',
    city_id: props.employee?.city_id ?? '',
    is_police_record: props.employee?.is_police_record === true || Number(props.employee?.is_police_record) === 1 ? 1 : (props.employee ? 0 : ''),
    address: props.employee?.address ?? '',
    description: props.employee?.description ?? '',
    employee_image: null,
    employee_signature: null,
});

watch([() => form.basic_salary, () => form.other_amount], ([basicSalary, otherAmount]) => {
    const basic = Number.parseFloat(basicSalary) || 0;
    const other = Number.parseFloat(otherAmount) || 0;
    form.net_salary = basic > 0 ? basic + other : '';
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
            <div class="col-6 align-self-start">
                <h4>{{ formTitle }}</h4>
            </div>
            <div class="col-6 align-self-end text-end mb-2">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row form-group">
                <div class="col-sm-3 mb-2">
                    <label for="employeeName">Name *</label>
                    <input id="employeeName" v-model="form.name" class="form-control" type="text" placeholder="Enter Employee name">
                    <FormError :message="form.errors.name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeFatherName">Father Name *</label>
                    <input id="employeeFatherName" v-model="form.guardian_name" class="form-control" type="text" placeholder="Enter Employee father name">
                    <FormError :message="form.errors.guardian_name" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeContactNo">Contact Number *</label>
                    <input id="employeeContactNo" v-model="form.contact_no" class="form-control" type="number" placeholder="Enter contact number">
                    <FormError :message="form.errors.contact_no" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeOtherNumber">other Number *</label>
                    <input id="employeeOtherNumber" v-model="form.other_number" class="form-control" type="number" placeholder="Enter other number">
                    <FormError :message="form.errors.other_number" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeEmail">Email *</label>
                    <input id="employeeEmail" v-model="form.email" class="form-control" type="text" placeholder="Enter email">
                    <FormError :message="form.errors.email" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeCnic">Employee CNIC *</label>
                    <input id="employeeCnic" v-model="form.cnic_no" class="form-control" type="number" placeholder="Enter cnic">
                    <FormError :message="form.errors.cnic_no" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeFatherCnic">Employee Father CNIC</label>
                    <input id="employeeFatherCnic" v-model="form.father_cnic_no" class="form-control" type="number" placeholder="Enter cnic">
                    <FormError :message="form.errors.father_cnic_no" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeDate_of_birth">Date of birth</label>
                    <input id="employeeDate_of_birth" v-model="form.date_of_birth" class="form-control" type="date">
                    <FormError :message="form.errors.date_of_birth" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="EmployeeType">Select Employee Type</label>
                    <div class="input-group">
                        <select id="EmployeeType" v-model="form.employee_type_id" class="form-control">
                            <option value="">Select Type</option>
                            <option v-for="item in employeeTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark" @click.prevent="openLookup('employee_types', 'employee_type_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.employee_type_id" />
                </div>
                <div class="col-3 mb-2">
                    <label class="font_bold" for="EmployeeLevelId">Select Employee Level</label>
                    <div class="input-group">
                        <select id="EmployeeLevelId" v-model="form.employee_level_id" class="form-control">
                            <option value="">Select Level</option>
                            <option v-for="item in employeeLevels" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <a href="#" class="btn input-group-text btn-dark" @click.prevent="openLookup('employee_levels', 'employee_level_id')">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                    <FormError :message="form.errors.employee_level_id" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeBasicSalary">Basic Salaray*</label>
                    <input id="employeeBasicSalary" v-model="form.basic_salary" class="form-control" type="number" min="0" step="any" placeholder="Enter Basic Salary">
                    <FormError :message="form.errors.basic_salary" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="empOtherAmount">Other Amount</label>
                    <input id="empOtherAmount" v-model="form.other_amount" class="form-control" type="number" min="0" step="any" placeholder="Enter other amounts">
                    <FormError :message="form.errors.other_amount" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="employeeNetSalary">Net Salary*</label>
                    <input id="employeeNetSalary" v-model="form.net_salary" class="form-control" type="number" min="0" step="any" readonly>
                    <FormError :message="form.errors.net_salary" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="empContractPeriod">Contract Period</label>
                    <input id="empContractPeriod" v-model="form.contract_period" class="form-control" type="text" placeholder="Enter Contract Period">
                    <FormError :message="form.errors.contract_period" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="empJoiningDate">Joining Date</label>
                    <input id="empJoiningDate" v-model="form.joining_date" class="form-control" type="date">
                    <FormError :message="form.errors.joining_date" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="empFarm">Select Farm</label>
                    <select id="empFarm" v-model="form.personal_farm_id" class="form-control">
                        <option value="">Select Farm</option>
                        <option v-for="item in personalFarms" :key="item.id" :value="item.id">{{ item.farm_name }} [{{ item.farm_address }}]</option>
                    </select>
                    <FormError :message="form.errors.personal_farm_id" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="bloodGroup">Blood Group</label>
                    <select id="bloodGroup" v-model="form.blood_group" class="form-control">
                        <option value="">Select Option</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                    <FormError :message="form.errors.blood_group" />
                </div>
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
                    <label class="font_bold" for="PoliceRecord">Police Record*</label>
                    <select id="PoliceRecord" v-model="form.is_police_record" class="form-control">
                        <option value="">Select Option</option>
                        <option :value="1">Yes</option>
                        <option :value="0">No</option>
                    </select>
                    <FormError :message="form.errors.is_police_record" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="ProfileImage">Profile Image</label>
                    <input id="ProfileImage" class="form-control" type="file" @change="form.employee_image = $event.target.files[0] ?? null">
                    <small v-if="! isCreate && employee?.employee_image" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.employee_image" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label for="empSignature">Employee Signature</label>
                    <input id="empSignature" class="form-control" type="file" @change="form.employee_signature = $event.target.files[0] ?? null">
                    <small v-if="! isCreate && employee?.employee_signature" class="text-muted">Leave blank to keep the current file.</small>
                    <FormError :message="form.errors.employee_signature" />
                </div>
                <div class="col-6 mb-2">
                    <label for="employeeAddress">Address*</label>
                    <input id="employeeAddress" v-model="form.address" class="form-control" type="text" placeholder="Enter address">
                    <FormError :message="form.errors.address" />
                </div>
                <div class="col-sm-12">
                    <label for="employeeDescription">Description</label>
                    <textarea id="employeeDescription" v-model="form.description" class="form-control" rows="2"></textarea>
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
                <div class="mb-2">
                    <label for="TypeName">Name *</label>
                    <input id="TypeName" v-model="lookupForm.name" class="form-control" type="text" placeholder="Enter name">
                    <FormError :message="lookupForm.errors.name" />
                </div>
                <PrimaryButton type="submit" :disabled="lookupForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="lookupOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
    </div>
</template>
