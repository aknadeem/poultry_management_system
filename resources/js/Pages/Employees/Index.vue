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
    employees: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const listUrl = route('inertia.employees.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.employees.current_page,
    perPage: props.employees.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'emp_code', label: 'Code' },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'cnic_no', label: 'CNIC', sortable: true },
    { key: 'contact_no', label: 'Contact', sortable: true },
    { key: 'farm_name', label: 'Farm' },
    { key: 'email', label: 'Email' },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroyEmployee(employee) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Employee: ${employee.name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.employees.destroy', employee));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Employees" :crumbs="['Home', 'FarmManagement', 'Employee']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Employees</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link :href="route('inertia.employees.create')" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-plus"></i> Employee
                                </Link>
                            </div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="employees.data"
                            :meta="paginatorMeta(employees)"
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
                                <Link :href="route('inertia.employees.show', row)" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-eye"></i> View
                                </Link>
                                <Link :href="route('inertia.employees.edit', row)" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </Link>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyEmployee(row)">
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
