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
    parties: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const route = useRoute();
const { can } = usePermissions();
const satellites = ref(null);
const listUrl = route('inertia.parties.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.parties.current_page,
    perPage: props.parties.per_page,
    filters: {
        party_type: props.filters.party_type,
    },
});
const confirm = useConfirm();
const columns = [
    { key: 'type_label', label: 'Type' },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'cnic_no', label: 'CNIC', sortable: true },
    { key: 'accounts', label: 'Account Detail' },
    { key: 'documents', label: 'Documents' },
    { key: 'balance_limits', label: 'Debit/Credit Limit' },
];
const hasFilters = computed(() => Boolean(table.state.filters.party_type));

function fetchList() {
    table.fetch(listUrl);
}

function onSearch(value) {
    table.state.search = value;
    table.state.page = 1;
    fetchList();
}

function onSort(column) {
    table.toggleSort(column, listUrl);
}

function onPage(page) {
    table.state.page = page;
    fetchList();
}

function onPageSize(size) {
    table.state.perPage = size;
    table.state.page = 1;
    fetchList();
}

function onTypeFilter(event) {
    table.state.filters.party_type = event.target.value;
    table.state.page = 1;
    fetchList();
}

function onReset() {
    table.reset(listUrl);
}

async function destroyParty(party) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Party: ${party.name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.parties.destroy', party));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'index']" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Parties</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link
                                    v-if="can('parties.create')"
                                    :href="route('inertia.parties.create')"
                                    class="btn btn-secondary btn-sm"
                                    title="Click to add new Party"
                                >
                                    <i class="fa fa-plus"></i>
                                    Create Party
                                </Link>
                            </div>
                        </div>

                        <div class="mb-2" style="max-width: 16rem">
                            <select class="form-control" :value="table.state.filters.party_type" @change="onTypeFilter">
                                <option value="">All Types</option>
                                <option value="customer">Customer</option>
                                <option value="vendor">Vendor</option>
                            </select>
                        </div>

                        <DataTable
                            :columns="columns"
                            :rows="parties.data"
                            :meta="paginatorMeta(parties)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            :has-filters="hasFilters"
                            actions-label="Options"
                            @update:search="onSearch"
                            @sort="onSort"
                            @page="onPage"
                            @page-size="onPageSize"
                            @reset="onReset"
                        >
                            <template #cell.accounts="{ row }">
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm" title="Click to View Accounts" @click="satellites.openViewAccounts(row)">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a
                                    v-if="can('parties.create')"
                                    href="javascript:void(0);"
                                    class="btn btn-success btn-sm"
                                    title="Click to Add New Account"
                                    @click="satellites.openAddAccount(row)"
                                >
                                    <i class="fa fa-plus"></i> add
                                </a>
                            </template>
                            <template #cell.documents="{ row }">
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm" title="Click to View Documents" @click="satellites.openViewDocuments(row)">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a
                                    v-if="can('parties.create')"
                                    href="javascript:void(0);"
                                    class="btn btn-success btn-sm"
                                    title="Click to Add New Document"
                                    @click="satellites.openAddDocument(row)"
                                >
                                    <i class="fa fa-plus"></i> add
                                </a>
                            </template>
                            <template #cell.balance_limits="{ row }">
                                <a href="javascript:void(0);" class="btn btn-secondary btn-sm" title="Click to View Limits" @click="satellites.openViewLimits(row)">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a
                                    v-if="can('parties.create')"
                                    href="javascript:void(0);"
                                    class="btn btn-success btn-sm"
                                    title="Click to Add New Limit"
                                    @click="satellites.openAddLimit(row)"
                                >
                                    <i class="fa fa-plus"></i> add
                                </a>
                            </template>
                            <template #actions="{ row }">
                                <Link
                                    v-if="can('parties.update')"
                                    :href="route('inertia.parties.edit', row)"
                                    class="btn btn-info btn-sm"
                                    title="Click to edit"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                    Edit
                                </Link>
                                <a
                                    v-if="can('parties.delete')"
                                    href="javascript:void(0);"
                                    class="btn btn-danger btn-sm"
                                    title="Click to delete"
                                    @click="destroyParty(row)"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

        <PartySatellites ref="satellites" />

        <ConfirmDialog
            :show="confirm.open.value"
            :title="confirm.title.value"
            :message="confirm.message.value"
            @confirm="confirm.confirm"
            @cancel="confirm.cancel"
        />
    </div>
</template>
