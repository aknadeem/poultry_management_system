<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import ConfirmDialog from '../../Components/Modal/ConfirmDialog.vue';
import FormModal from '../../Components/Modal/FormModal.vue';
import FormError from '../../Components/Forms/FormError.vue';
import PrimaryButton from '../../Components/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Components/Buttons/SecondaryButton.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useConfirm } from '../../Composables/useConfirm';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    schedules: { type: Object, required: true },
    filters: { type: Object, required: true },
    customerFarms: { type: Array, default: () => [] },
    products: { type: Array, default: () => [] },
});

const route = useRoute();
const listUrl = route('inertia.vaccinations.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.schedules.current_page,
    perPage: props.schedules.per_page,
});
const confirm = useConfirm();
const scheduleOpen = ref(false);
const recordOpen = ref(false);
const recording = ref(null);
const scheduleForm = useForm({
    farm_id: '',
    product_id: '',
    schedule_date: '',
    description: '',
});
const recordForm = useForm({
    schedule_id: '',
    vaccination_date: new Date().toISOString().slice(0, 10),
    remarks: '',
});
const columns = [
    { key: 'farm_name', label: 'Farm' },
    { key: 'product_name', label: 'Vaccine' },
    { key: 'schedule_date', label: 'Schedule Date', sortable: true },
    { key: 'is_vaccinated', label: 'Vaccinated' },
    { key: 'description', label: 'Description' },
    { key: 'is_active', label: 'Active' },
];

function fetchList() {
    table.fetch(listUrl);
}

function openSchedule() {
    scheduleForm.reset();
    scheduleForm.clearErrors();
    scheduleOpen.value = true;
}

function submitSchedule() {
    scheduleForm.post(route('inertia.vaccinations.store'), {
        preserveScroll: true,
        onSuccess: () => {
            scheduleOpen.value = false;
        },
    });
}

function openRecord(schedule) {
    recording.value = schedule;
    recordForm.reset();
    recordForm.clearErrors();
    recordForm.schedule_id = schedule.id;
    recordForm.vaccination_date = new Date().toISOString().slice(0, 10);
    recordOpen.value = true;
}

function submitRecord() {
    recordForm.post(route('inertia.vaccinations.record'), {
        preserveScroll: true,
        onSuccess: () => {
            recordOpen.value = false;
        },
    });
}

async function toggleStatus(schedule) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: 'Are you sure you want to update this schedule status?',
    });

    if (confirmed) {
        router.put(route('inertia.vaccinations.toggle-status', schedule));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Vaccination" :crumbs="['Home', 'FarmManagement', 'Vaccination']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Vaccination Schedule</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <button type="button" class="btn btn-secondary btn-sm" @click="openSchedule">
                                    <i class="fa fa-plus"></i> Add Schedule
                                </button>
                            </div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="schedules.data"
                            :meta="paginatorMeta(schedules)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            actions-label="Options"
                            @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                            @sort="(column) => table.toggleSort(column, listUrl)"
                            @page="(page) => { table.state.page = page; fetchList(); }"
                            @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                            @reset="table.reset(listUrl)"
                        >
                            <template #cell.is_vaccinated="{ row }">
                                <span :class="row.is_vaccinated ? 'text-success' : 'text-danger'">
                                    {{ row.is_vaccinated ? 'Yes' : 'Not Vaccinated' }}
                                    <template v-if="row.is_vaccinated && row.vaccination_date"><br>At: {{ row.vaccination_date }}</template>
                                </span>
                            </template>
                            <template #cell.is_active="{ row }">
                                <a href="javascript:void(0);" @click="toggleStatus(row)">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            :class="row.is_active ? 'bg-success border-success' : 'bg-danger border-danger'"
                                            type="checkbox"
                                            :checked="row.is_active"
                                            readonly
                                        >
                                    </div>
                                </a>
                            </template>
                            <template #actions="{ row }">
                                <a href="javascript:void(0);" class="btn btn-info btn-sm" @click="openRecord(row)">
                                    <i class="fas fa-syringe"></i> Add Vaccination
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <FormModal :show="scheduleOpen" title="Add Vaccination Schedule" @close="scheduleOpen = false">
            <form autocomplete="off" @submit.prevent="submitSchedule">
                <div class="mb-2">
                    <label>Farm *</label>
                    <select v-model="scheduleForm.farm_id" class="form-control">
                        <option value="">Select farm</option>
                        <option v-for="item in customerFarms" :key="item.id" :value="item.id">{{ item.farm_name }}</option>
                    </select>
                    <FormError :message="scheduleForm.errors.farm_id" />
                </div>
                <div class="mb-2">
                    <label>Vaccine *</label>
                    <select v-model="scheduleForm.product_id" class="form-control">
                        <option value="">Select vaccine</option>
                        <option v-for="item in products" :key="item.id" :value="item.id">{{ item.product_code }} - {{ item.product_name }}</option>
                    </select>
                    <FormError :message="scheduleForm.errors.product_id" />
                </div>
                <div class="mb-2">
                    <label>Schedule Date *</label>
                    <input v-model="scheduleForm.schedule_date" class="form-control" type="date">
                    <FormError :message="scheduleForm.errors.schedule_date" />
                </div>
                <div class="mb-2">
                    <label>Description</label>
                    <textarea v-model="scheduleForm.description" class="form-control" rows="2"></textarea>
                    <FormError :message="scheduleForm.errors.description" />
                </div>
                <PrimaryButton type="submit" :disabled="scheduleForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="scheduleOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
        <FormModal :show="recordOpen" title="Add Vaccination" @close="recordOpen = false">
            <form autocomplete="off" @submit.prevent="submitRecord">
                <p class="mb-2"><b>Farm:</b> {{ recording?.farm_name }}</p>
                <p class="mb-2"><b>Vaccine:</b> {{ recording?.product_code }} {{ recording?.product_name }}</p>
                <div class="mb-2">
                    <label>Vaccination Date *</label>
                    <input v-model="recordForm.vaccination_date" class="form-control" type="date">
                    <FormError :message="recordForm.errors.vaccination_date" />
                </div>
                <div class="mb-2">
                    <label>Remarks</label>
                    <textarea v-model="recordForm.remarks" class="form-control" rows="2"></textarea>
                    <FormError :message="recordForm.errors.remarks" />
                </div>
                <PrimaryButton type="submit" :disabled="recordForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="recordOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
