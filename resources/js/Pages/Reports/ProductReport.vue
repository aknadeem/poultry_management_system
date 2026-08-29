<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ReportDateFilter from '../../Components/Reports/ReportDateFilter.vue';
import ReportResultsTable from '../../Components/Reports/ReportResultsTable.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    products: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const route = useRoute();
const showResults = ref(Boolean(props.filters.searched));

const columns = [
    { key: 'date_from', label: 'Date From' },
    { key: 'date_to', label: 'Date To' },
    { key: 'company_name', label: 'Company' },
    { key: 'product_code', label: 'Product Code' },
    { key: 'product_group', label: 'Product Group' },
    { key: 'product_name', label: 'Product Name' },
    { key: 'quantity', label: 'Quantity' },
];

function searchDates(dates) {
    showResults.value = true;
    router.get(route('inertia.reports.product'), dates, {
        preserveState: false,
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Report" :crumbs="['Home', 'ReportManagement', 'ProductReport']" />
        <ReportDateFilter
            :from-date="filters.from_date"
            :to-date="filters.to_date"
            @search="searchDates"
        />
        <ReportResultsTable
            v-if="showResults"
            title="Product Report"
            :columns="columns"
            :rows="products"
            :exact-match-keys="['party_company_id']"
        >
            <template #filters="{ setFilter, filters: columnFilters }">
                <tr>
                    <td class="form-group" colspan="4">
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
                    <td class="form-group fs-5" colspan="2">
                        <label class="form-control-label">Search by <b>Product Code</b></label>
                        <input
                            type="text"
                            class="form-control px-1"
                            placeholder="Enter product code"
                            :value="columnFilters.product_code ?? ''"
                            @input="setFilter('product_code', $event.target.value)"
                        >
                    </td>
                    <td class="form-group fs-5" colspan="2">
                        <label class="form-control-label">Search by <b>Product Group</b></label>
                        <input
                            type="text"
                            class="form-control px-1"
                            placeholder="Enter product group"
                            :value="columnFilters.product_group ?? ''"
                            @input="setFilter('product_group', $event.target.value)"
                        >
                    </td>
                </tr>
            </template>
        </ReportResultsTable>
    </div>
</template>
