<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import ConfirmDialog from '../../Components/Modal/ConfirmDialog.vue';
import PartySatellites from '../../Components/Parties/PartySatellites.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useConfirm } from '../../Composables/useConfirm';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    vendors: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const satellites = ref(null);
const listUrl = route('inertia.vendors.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.vendors.current_page,
    perPage: props.vendors.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'cnic_no', label: 'CNIC', sortable: true },
    { key: 'contact_no', label: 'Contact Number', sortable: true },
    { key: 'company_name', label: 'Company' },
    { key: 'accounts', label: 'Accounts' },
    { key: 'documents', label: 'Documents' },
    { key: 'balance_limits', label: 'Balance Limit' },
];
const hasFilters = computed(() => false);

function fetchList() {
    table.fetch(listUrl);
}

async function destroyVendor(vendor) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Vendor: ${vendor.name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.vendors.destroy', vendor));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Vendors" :crumbs="['Home', 'Vendor']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start"><h4>Vendors</h4></div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link v-if="can('parties.create')" :href="route('inertia.vendors.create')" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-plus"></i> Vendor
                                </Link>
                            </div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="vendors.data"
                            :meta="paginatorMeta(vendors)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            :has-filters="hasFilters"
                            actions-label="Options"
                            @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                            @sort="(column) => table.toggleSort(column, listUrl)"
                            @page="(page) => { table.state.page = page; fetchList(); }"
                            @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                            @reset="table.reset(listUrl)"
                        >
                            <template #cell.accounts="{ row }">
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm" title="Click to View Accounts" @click="satellites.openViewAccounts(row)">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a v-if="can('parties.create')" href="javascript:void(0);" class="btn btn-success btn-sm" title="Click to Add New Account" @click="satellites.openAddAccount(row)">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </template>
                            <template #cell.documents="{ row }">
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm" title="Click to View Documents" @click="satellites.openViewDocuments(row)">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a v-if="can('parties.create')" href="javascript:void(0);" class="btn btn-success btn-sm" title="Click to Add New Document" @click="satellites.openAddDocument(row)">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </template>
                            <template #cell.balance_limits="{ row }">
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm" title="Click to View Limits" @click="satellites.openViewLimits(row)">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a v-if="can('parties.create')" href="javascript:void(0);" class="btn btn-success btn-sm" title="Click to Add New Limit" @click="satellites.openAddLimit(row)">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </template>
                            <template #actions="{ row }">
                                <Link v-if="can('parties.view')" :href="route('inertia.vendors.show', row)" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-eye"></i> View
                                </Link>
                                <Link v-if="can('parties.update')" :href="route('inertia.vendors.edit', row)" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </Link>
                                <a v-if="can('parties.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyVendor(row)">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <PartySatellites ref="satellites" />
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
