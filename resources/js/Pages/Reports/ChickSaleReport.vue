<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ReportDateFilter from '../../Components/Reports/ReportDateFilter.vue';
import ReportResultsTable from '../../Components/Reports/ReportResultsTable.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    sales: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const route = useRoute();
const showResults = ref(Boolean(props.filters.searched));

const columns = [
    { key: 'date_from', label: 'Date From' },
    { key: 'date_to', label: 'Date To' },
    { key: 'customer', label: 'Customer', html: true },
    { key: 'party_id', label: 'Party Hidden', hidden: true },
    { key: 'manual_number', label: 'manual_number', hidden: true },
    { key: 'sale_date', label: 'Sale Date' },
    { key: 'per_kg_price', label: 'PerKgPrice' },
    { key: 'total_weight', label: 'TotalWeight' },
    { key: 'discount_amount', label: 'Dicount Amount' },
    { key: 'total_price', label: 'total Price' },
];

function searchDates(dates) {
    showResults.value = true;
    router.get(route('inertia.reports.chick-sale'), dates, {
        preserveState: false,
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicken Sales Report" :crumbs="['Home', 'ReportManagement', 'ChickenSales']" />
        <ReportDateFilter
            :from-date="filters.from_date"
            :to-date="filters.to_date"
            @search="searchDates"
        />
        <ReportResultsTable
            v-if="showResults"
            title="Chicken Sale Report"
            :columns="columns"
            :rows="sales"
            :exact-match-keys="['party_id', 'sale_date']"
        >
            <template #filters="{ setFilter, filters: columnFilters }">
                <tr>
                    <td class="form-group" colspan="4">
                        <label class="form-control-label fs-5">Search by <b>Customer</b></label>
                        <select
                            class="form-control px-1"
                            :value="columnFilters.party_id ?? ''"
                            @change="setFilter('party_id', $event.target.value)"
                        >
                            <option value="">Select Customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name }}
                            </option>
                            <option value="all">All</option>
                        </select>
                    </td>
                    <td class="form-group" colspan="4">
                        <label class="form-control-label">Search by <b>Manual Number</b></label>
                        <input
                            type="text"
                            class="form-control px-1"
                            placeholder="Enter manual number"
                            :value="columnFilters.manual_number ?? ''"
                            @input="setFilter('manual_number', $event.target.value)"
                        >
                    </td>
                    <td class="form-group" colspan="3">
                        <label class="form-control-label">Search by <b>Sale date</b></label>
                        <input
                            type="date"
                            class="form-control px-1"
                            :value="columnFilters.sale_date ?? ''"
                            @input="setFilter('sale_date', $event.target.value)"
                        >
                    </td>
                </tr>
            </template>
            <template #cell.customer="{ row }">
                <span v-html="row.customer"></span>
            </template>
        </ReportResultsTable>
    </div>
</template>
