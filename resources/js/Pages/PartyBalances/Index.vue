<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    balances: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.party-balances.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.balances.current_page,
    perPage: props.balances.per_page,
});
const columns = [
    { key: 'party_name', label: 'Party' },
    { key: 'total_amount', label: 'Total', sortable: true, align: 'end' },
    { key: 'paid_amount', label: 'Paid', align: 'end' },
    { key: 'remaining_amount', label: 'Remaining', sortable: true, align: 'end' },
    { key: 'amount_type', label: 'Type' },
    { key: 'payment_status', label: 'Status' },
    { key: 'transaction_date', label: 'Date', sortable: true },
];
const hasFilters = computed(() => false);

function fetchList() {
    table.fetch(listUrl);
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Balance" :crumbs="['Home', 'PartyManagement', 'Party Balance']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start"><h4>Party Balances</h4></div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="balances.data"
                            :meta="paginatorMeta(balances)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            :has-filters="hasFilters"
                            @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                            @sort="(column) => table.toggleSort(column, listUrl)"
                            @page="(page) => { table.state.page = page; fetchList(); }"
                            @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                            @reset="table.reset(listUrl)"
                        >
                            <template #actions="{ row }">
                                <Link v-if="can('partyBalances.view')" :href="route('inertia.party-balances.show', row)" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-eye"></i> View
                                </Link>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
