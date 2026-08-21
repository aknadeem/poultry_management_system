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
    farms: { type: Object, required: true },
    filters: { type: Object, required: true },
    farmTypes: { type: Array, default: () => [] },
    farmSubtypes: { type: Array, default: () => [] },
});

const route = useRoute();
const listUrl = route('inertia.customer-farms.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.farms.current_page,
    perPage: props.farms.per_page,
});
const confirm = useConfirm();
const editingFarm = ref(null);
const form = useForm({
    farm_type_id: '',
    farm_subtype_id: '',
    farm_name: '',
    farm_noc: '',
    farm_address: '',
    farm_area: '',
    feed_room_size: '',
    farm_capacity: '',
    farm_image: null,
});
const columns = [
    { key: 'farm_type_name', label: 'Type' },
    { key: 'farm_subtype_name', label: 'Subtype' },
    { key: 'farm_name', label: 'Farm Name', sortable: true },
    { key: 'party_name', label: 'Customer' },
    { key: 'party_cnic_no', label: 'CNIC' },
];

function fetchList() {
    table.fetch(listUrl);
}

function openEdit(farm) {
    editingFarm.value = farm;
    form.clearErrors();
    form.farm_type_id = farm.farm_type_id ?? '';
    form.farm_subtype_id = farm.farm_subtype_id ?? '';
    form.farm_name = farm.farm_name ?? '';
    form.farm_noc = farm.farm_noc ?? '';
    form.farm_address = farm.farm_address ?? '';
    form.farm_area = farm.farm_area ?? '';
    form.feed_room_size = farm.feed_room_size ?? '';
    form.farm_capacity = farm.farm_capacity ?? '';
    form.farm_image = null;
}

function submitEdit() {
    form.submit('put', route('inertia.customer-farms.update', editingFarm.value), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editingFarm.value = null;
        },
    });
}

async function destroyFarm(farm) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Farm: ${farm.farm_name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.customer-farms.destroy', farm));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Customer Farms" :crumbs="['Home', 'FarmManagement', 'CustomerFarms']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Customer Farms</h4>
                            </div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="farms.data"
                            :meta="paginatorMeta(farms)"
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
                            <template #actions="{ row }">
                                <a href="javascript:void(0);" class="btn btn-info btn-sm" @click="openEdit(row)">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyFarm(row)">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <FormModal :show="Boolean(editingFarm)" title="Edit Customer Farm" @close="editingFarm = null">
            <form autocomplete="off" @submit.prevent="submitEdit">
                <div class="row">
                    <div class="col-6 mb-2">
                        <label>Customer Name</label>
                        <input class="form-control" type="text" :value="editingFarm?.party_name" disabled>
                    </div>
                    <div class="col-6 mb-2">
                        <label>CNIC</label>
                        <input class="form-control" type="text" :value="editingFarm?.party_cnic_no" disabled>
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Type *</label>
                        <select v-model="form.farm_type_id" class="form-control">
                            <option value="">Select Farm Type</option>
                            <option v-for="item in farmTypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <FormError :message="form.errors.farm_type_id" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Subtype *</label>
                        <select v-model="form.farm_subtype_id" class="form-control">
                            <option value="">Select Farm subtype</option>
                            <option v-for="item in farmSubtypes" :key="item.id" :value="item.id">{{ item.name }}</option>
                        </select>
                        <FormError :message="form.errors.farm_subtype_id" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Name *</label>
                        <input v-model="form.farm_name" class="form-control" type="text">
                        <FormError :message="form.errors.farm_name" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm NOC *</label>
                        <input v-model="form.farm_noc" class="form-control" type="text">
                        <FormError :message="form.errors.farm_noc" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Address *</label>
                        <input v-model="form.farm_address" class="form-control" type="text">
                        <FormError :message="form.errors.farm_address" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Area</label>
                        <input v-model="form.farm_area" class="form-control" type="number" min="0" step="any">
                        <FormError :message="form.errors.farm_area" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Feed Room Size</label>
                        <input v-model="form.feed_room_size" class="form-control" type="number" min="0" step="any">
                        <FormError :message="form.errors.feed_room_size" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Capacity</label>
                        <input v-model="form.farm_capacity" class="form-control" type="number" min="0">
                        <FormError :message="form.errors.farm_capacity" />
                    </div>
                    <div class="col-6 mb-2">
                        <label>Farm Image</label>
                        <input class="form-control" type="file" @change="form.farm_image = $event.target.files[0] ?? null">
                        <small v-if="editingFarm?.farm_image_url" class="text-muted d-block">Leave blank to keep the current file.</small>
                        <FormError :message="form.errors.farm_image" />
                    </div>
                </div>
                <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="editingFarm = null">Cancel</SecondaryButton>
            </form>
        </FormModal>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
