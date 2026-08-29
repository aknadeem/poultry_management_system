<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import ConfirmDialog from '../../Components/Modal/ConfirmDialog.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useConfirm } from '../../Composables/useConfirm';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    companies: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.companies.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.companies.current_page,
    perPage: props.companies.per_page,
});
const confirmDialog = useConfirm();
const dialogVariant = ref('danger');

const columns = [
    { key: 'company_logo_url', label: 'Logo' },
    { key: 'company_name', label: 'Name', sortable: true },
    { key: 'business_type', label: 'Business Type' },
    { key: 'vendor_name', label: 'Vendor Name' },
    { key: 'is_active', label: 'Status' },
    { key: 'company_address', label: 'Address', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroyCompany(company) {
    dialogVariant.value = 'danger';
    const confirmed = await confirmDialog.ask({
        title: 'Please Confirm!',
        message: `Are you sure you want to delete company: ${company.company_name}?`,
    });
    if (confirmed) {
        router.delete(route('inertia.companies.destroy', company));
    }
}

async function askToggleStatus(company) {
    dialogVariant.value = 'modern';
    const confirmed = await confirmDialog.ask({
        title: 'Confirm Please?',
        message: 'Are you sure to continue?',
    });
    if (confirmed) {
        router.put(route('inertia.companies.toggle-status', company));
    }
}

function onConfirm() { confirmDialog.confirm(); }
function onCancel() { confirmDialog.cancel(); }
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Company" :crumbs="['Home', 'Company']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Companies</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('companies.create')" :href="route('inertia.companies.create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Company
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="companies.data"
                    :meta="paginatorMeta(companies)"
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
                    <template #cell.company_logo_url="{ row }">
                        <img
                            v-if="row.company_logo_url"
                            class="rounded-circle avatar-lg"
                            :src="row.company_logo_url"
                            alt="Company logo"
                            style="width: 48px; height: 48px; object-fit: cover;"
                        >
                        <b v-else>No Image</b>
                    </template>
                    <template #cell.company_name="{ row }">
                        <b>{{ row.company_name }}</b>
                    </template>
                    <template #cell.vendor_name="{ row }">
                        <b>{{ row.vendor_name || '—' }}</b>
                    </template>
                    <template #cell.is_active="{ row }">
                        <button
                            v-if="can('companies.update')"
                            type="button"
                            class="btn btn-link p-0 border-0 align-baseline"
                            title="Click to update Status"
                            @click.prevent="askToggleStatus(row)"
                        >
                            <div class="form-check form-switch m-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :checked="Boolean(row.is_active)"
                                    tabindex="-1"
                                    @click.prevent
                                >
                            </div>
                        </button>
                        <div v-else class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" :checked="Boolean(row.is_active)" disabled>
                        </div>
                    </template>
                    <template #actions="{ row }">
                        <Link v-if="can('companies.view')" :href="route('inertia.companies.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                        <Link v-if="can('companies.update')" :href="route('inertia.companies.edit', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <a v-if="can('companies.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyCompany(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog
            :show="confirmDialog.open.value"
            :title="confirmDialog.title.value"
            :message="confirmDialog.message.value"
            :variant="dialogVariant"
            @confirm="onConfirm"
            @cancel="onCancel"
        />
    </div>
</template>
