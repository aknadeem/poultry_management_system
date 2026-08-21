<script setup>
import { Link, router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import ConfirmDialog from '../../Components/Modal/ConfirmDialog.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useConfirm } from '../../Composables/useConfirm';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    farms: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const listUrl = route('inertia.personal-farms.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.farms.current_page,
    perPage: props.farms.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'farm_code', label: 'Code', sortable: true },
    { key: 'farm_name', label: 'Farm Name', sortable: true },
    { key: 'farm_type_name', label: 'Type' },
    { key: 'farm_subtype_name', label: 'Subtype' },
    { key: 'farm_capacity', label: 'Capacity', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroyFarm(farm) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Farm: ${farm.farm_name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.personal-farms.destroy', farm));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Personal Farms" :crumbs="['Home', 'FarmManagement', 'PersonalFarms']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Personal Farms</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link :href="route('inertia.personal-farms.create')" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-plus"></i> Create Farm
                                </Link>
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
                                <Link :href="route('inertia.personal-farms.edit', row)" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </Link>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyFarm(row)">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
