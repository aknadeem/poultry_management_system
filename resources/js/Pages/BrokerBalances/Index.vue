<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    balances: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const listUrl = route('inertia.broker-balances.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.balances.current_page,
    perPage: props.balances.per_page,
});

const columns = [
    { key: 'narration', label: 'Narration' },
    { key: 'broker_name', label: 'Broker' },
    { key: 'total_amount', label: 'Total Amount', sortable: true },
    { key: 'paid_amount', label: 'Paid Amount' },
    { key: 'remaining_amount', label: 'Remaining Amount', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Date', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Broker Balance" :crumbs="['Home', 'PartyManagement', 'Broker Balance']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Broker Balances</h4></div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="balances.data"
                    :meta="paginatorMeta(balances)"
                    :search="table.state.search"
                    :sort="table.state.sort"
                    :direction="table.state.direction"
                    :loading="table.processing.value"
                    @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                    @sort="(column) => table.toggleSort(column, listUrl)"
                    @page="(page) => { table.state.page = page; fetchList(); }"
                    @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                    @reset="table.reset(listUrl)"
                >
                    <template #cell.broker_name="{ row }">
                        <b>{{ row.broker_name || '—' }}</b>
                    </template>
                    <template #cell.status="{ row }">
                        <span class="badge bg-secondary">{{ row.status }}</span>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>
