<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ReportDateFilter from '../../Components/Reports/ReportDateFilter.vue';
import ReportResultsTable from '../../Components/Reports/ReportResultsTable.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    sales: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const route = useRoute();
const showResults = ref(Boolean(props.filters.searched));

const columns = [
    { key: 'date_from', label: 'Date From' },
    { key: 'date_to', label: 'Date To' },
    { key: 'company_name', label: 'Company' },
    { key: 'sale_code', label: 'Code' },
    { key: 'party_name', label: 'party' },
    { key: 'sale_date', label: 'Sale Date' },
    { key: 'total_amount', label: 'Total Amount' },
    { key: 'discount_amount', label: 'Discount Amount' },
    { key: 'final_amount', label: 'Final Amount' },
];

function searchDates(dates) {
    showResults.value = true;
    router.get(route('inertia.reports.product-sale'), dates, {
        preserveState: false,
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Sale Report" :crumbs="['Home', 'ReportManagement', 'ProductSaleReport']" />
        <ReportDateFilter
            :from-date="filters.from_date"
            :to-date="filters.to_date"
            @search="searchDates"
        />
        <ReportResultsTable
            v-if="showResults"
            title="Product Sale Report"
            :columns="columns"
            :rows="sales"
            :exact-match-keys="['party_company_id']"
        >
            <template #filters="{ setFilter, filters: columnFilters }">
                <tr>
                    <td class="form-group" colspan="5">
                        <label class="form-control-label fs-5">Search by <b>Company</b></label>
                        <select
                            class="form-control px-1"
                            :value="columnFilters.party_company_id ?? ''"
                            @change="setFilter('party_company_id', $event.target.value)"
                        >
                            <option value="">Select Company</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">
                                {{ company.company_name }}
                            </option>
                            <option value="all">All</option>
                        </select>
                    </td>
                    <td class="form-group fs-5" colspan="5">
                        <label class="form-control-label">Search by <b>Sale Code</b></label>
                        <input
                            type="text"
                            class="form-control px-1"
                            placeholder="Enter sale code"
                            :value="columnFilters.sale_code ?? ''"
                            @input="setFilter('sale_code', $event.target.value)"
                        >
                    </td>
                </tr>
            </template>
        </ReportResultsTable>
    </div>
</template>
