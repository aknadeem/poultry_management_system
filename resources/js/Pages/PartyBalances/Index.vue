<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import PaymentForm from '../../Components/PartyBalances/PaymentForm.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    balances: { type: Object, required: true },
    filters: { type: Object, required: true },
    today: { type: String, default: '' },
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
    { key: 'amount_type', label: 'Type' },
    { key: 'party_name', label: 'Party' },
    { key: 'total_amount', label: 'Total Amount', sortable: true },
    { key: 'paid_amount', label: 'Paid Amount' },
    { key: 'remaining_amount', label: 'Remaining Amount', sortable: true },
    { key: 'payment_status', label: 'Status' },
    { key: 'narration', label: 'narration' },
    { key: 'transaction_date', label: 'Date', sortable: true },
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

function statusClass(color) {
    return `bg-${color || 'secondary'}`;
}

function paymentButtonClass(color) {
    return `btn-${color || 'secondary'}`;
}

function formatAmount(value) {
    return Number(value || 0).toLocaleString();
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Balance List" :crumbs="['Home', 'BalanceManagement', 'PartyBalance']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start"><h4>Party Balance</h4></div>
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
                            <template #cell.amount_type="{ row }">
                                <span class="fw-bold fs-5" :class="`text-${row.amount_type_color || 'secondary'}`">
                                    {{ row.amount_type }}
                                </span>
                            </template>
                            <template #cell.total_amount="{ row }">{{ formatAmount(row.total_amount) }}</template>
                            <template #cell.paid_amount="{ row }">{{ formatAmount(row.paid_amount) }}</template>
                            <template #cell.remaining_amount="{ row }">
                                <span class="text-danger fw-bold fs-5">{{ formatAmount(row.remaining_amount) }}</span>
                            </template>
                            <template #cell.payment_status="{ row }">
                                <span class="badge" :class="statusClass(row.payment_status_color)">
                                    {{ row.payment_status }}
                                </span>
                            </template>
                            <template #actions="{ row }">
                                <button
                                    v-if="can('partyBalances.create')"
                                    type="button"
                                    class="btn btn-bold btn-sm me-1"
                                    :class="paymentButtonClass(row.amount_type_color)"
                                    title="Click to add payment"
                                    @click="openPaymentModal(row)"
                                >
                                    <i class="fa fa-plus"></i>
                                    Payment
                                </button>
                                <Link
                                    v-if="can('partyBalances.view')"
                                    :href="route('inertia.party-balances.show', row)"
                                    class="btn btn-info btn-bold btn-sm"
                                    title="Click to view payments"
                                >
                                    <i class="fa fa-eye"></i>
                                    view
                                </Link>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

        <PaymentForm
            v-if="selectedBalance"
            :show="showPaymentModal"
            :balance="selectedBalance"
            :today="today"
            @close="onPaymentModalClose"
        />
    </div>
</template>
