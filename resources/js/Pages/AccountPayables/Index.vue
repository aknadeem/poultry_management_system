<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    payables: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const listUrl = route('inertia.payables.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.payables.current_page,
    perPage: props.payables.per_page,
});

const columns = [
    { key: 'entry_date', label: 'Date', sortable: true },
    { key: 'total_amount', label: 'Total Amount', sortable: true },
    { key: 'paid_amount', label: 'Paid Amount' },
    { key: 'remaining_amount', label: 'Remaining Amount', sortable: true },
    { key: 'amount_status', label: 'Amount Status', sortable: true },
    { key: 'amount_type', label: 'Amount Type', sortable: true },
];

function money(value) {
    return `Rs: ${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function fetchList() {
    table.fetch(listUrl);
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Payments" :crumbs="['Home', 'PaymentManagement']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Account Payable</h4></div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="payables.data"
                    :meta="paginatorMeta(payables)"
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
                    <template #cell.entry_date="{ row }">
                        {{ row.entry_date_label || row.entry_date || '—' }}
                    </template>
                    <template #cell.total_amount="{ row }">
                        {{ money(row.total_amount) }}
                    </template>
                    <template #cell.paid_amount="{ row }">
                        {{ money(row.paid_amount) }}
                    </template>
                    <template #cell.remaining_amount="{ row }">
                        {{ money(row.remaining_amount) }}
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>
