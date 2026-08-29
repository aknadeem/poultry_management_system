<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ReportDateFilter from '../../Components/Reports/ReportDateFilter.vue';
import ReportResultsTable from '../../Components/Reports/ReportResultsTable.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    purchases: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const route = useRoute();
const showResults = ref(Boolean(props.filters.searched));

const columns = [
    { key: 'date_from', label: 'Date From' },
    { key: 'date_to', label: 'Date To' },
    { key: 'company_name', label: 'Company' },
    { key: 'purchase_date', label: 'Purchase Date' },
    { key: 'price', label: 'Price' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'discount_amount', label: 'Dicount Amount' },
    { key: 'total_price', label: 'total Price' },
];

function searchDates(dates) {
    showResults.value = true;
    router.get(route('inertia.reports.chick-purchase'), dates, {
        preserveState: false,
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chick Purchase Report" :crumbs="['Home', 'ReportManagement', 'ChickPurchase']" />
        <ReportDateFilter
            :from-date="filters.from_date"
            :to-date="filters.to_date"
            @search="searchDates"
        />
        <ReportResultsTable
            v-if="showResults"
            title="Chicken Purchase Report"
            :columns="columns"
            :rows="purchases"
        >
            <template #filters="{ setFilter, filters: columnFilters }">
                <tr>
                    <td class="form-group" colspan="4">
                        <label class="form-control-label fs-5">Search by <b>Company</b></label>
                        <input
                            type="text"
                            class="form-control px-1"
                            placeholder="Enter company name"
                            :value="columnFilters.company_name ?? ''"
                            @input="setFilter('company_name', $event.target.value)"
                        >
                    </td>
                    <td class="form-group" colspan="4">
                        <label class="form-control-label">Search by <b>Purchase date</b></label>
                        <input
                            type="date"
                            class="form-control px-1"
                            :value="columnFilters.purchase_date ?? ''"
                            @input="setFilter('purchase_date', $event.target.value)"
                        >
                    </td>
                </tr>
            </template>
        </ReportResultsTable>
    </div>
</template>
