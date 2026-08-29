<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import PaymentForm from '../../Components/CompanyBalances/PaymentForm.vue';
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
const listUrl = route('inertia.company-balances.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.balances.current_page,
    perPage: props.balances.per_page,
});

const columns = [
    { key: 'type', label: 'Type', sortable: true },
    { key: 'company_name', label: 'Company' },
    { key: 'total_amount', label: 'Total Amount', sortable: true },
    { key: 'paid_amount', label: 'Paid Amount', sortable: true },
    { key: 'remaining_amount', label: 'Remaining Amount', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'created_at', label: 'Date', sortable: true },
];

const selectedBalance = ref(null);
const showPaymentModal = ref(false);

function fetchList() {
    table.fetch(listUrl);
}

function openPaymentModal(balance) {
    selectedBalance.value = balance;
    showPaymentModal.value = true;
}

function onPaymentModalClose() {
    showPaymentModal.value = false;
    selectedBalance.value = null;
    router.reload({ only: ['balances', 'filters'] });
}

function statusClass(status) {
    const value = String(status || '').toLowerCase();
    if (value === 'paid') {
        return 'bg-success';
    }
    if (value === 'unpaid') {
        return 'bg-danger';
    }

    return 'bg-warning';
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Companies Balance" :crumbs="['Home', 'Companies Balance']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Companies Balance</h4></div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="balances.data"
                    :meta="paginatorMeta(balances)"
                    :search="table.state.search"
                    :sort="table.state.sort"
                    :direction="table.state.direction"
                    :loading="table.processing.value"
                    actions-label="Action"
                    @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                    @sort="(column) => table.toggleSort(column, listUrl)"
                    @page="(page) => { table.state.page = page; fetchList(); }"
                    @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                    @reset="table.reset(listUrl)"
                >
                    <template #cell.status="{ row }">
                        <span class="badge" :class="statusClass(row.status)">
                            {{ row.status }}
                        </span>
                    </template>
                    <template #cell.total_amount="{ row }">{{ Number(row.total_amount).toLocaleString() }}</template>
                    <template #cell.paid_amount="{ row }">{{ Number(row.paid_amount).toLocaleString() }}</template>
                    <template #cell.remaining_amount="{ row }">
                        <span class="fw-bold text-danger">{{ Number(row.remaining_amount).toLocaleString() }}</span>
                    </template>
                    <template #actions="{ row }">
                        <button
                            v-if="can('companyBalances.create') && Number(row.remaining_amount) > 0"
                            type="button"
                            class="btn btn-primary btn-sm me-1"
                            @click="openPaymentModal(row)"
                        >
                            <i class="fa fa-money-bill"></i> Payment
                        </button>
                        <Link v-if="can('companyBalances.view')" :href="route('inertia.company-balances.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                    </template>
                </DataTable>
            </div>
        </div>

        <PaymentForm
            v-if="selectedBalance"
            :show="showPaymentModal"
            :company-balance="selectedBalance"
            @close="onPaymentModalClose"
        />
    </div>
</template>
