<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    sales: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.product-sales.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.sales.current_page,
    perPage: props.sales.per_page,
});
const columns = [
    { key: 'company_name', label: 'Company' },
    { key: 'category_name', label: 'Category' },
    { key: 'party_name', label: 'Customer' },
    { key: 'sale_date_label', label: 'Date', sortable: true },
    { key: 'payment_status_label', label: 'Payment' },
    { key: 'total_amount', label: 'Total Price', sortable: true },
    { key: 'discount_amount', label: 'Discount' },
    { key: 'other_charges', label: 'Tax' },
    { key: 'rebate_amount', label: 'Rebate Amount' },
    { key: 'final_amount', label: 'Final Price', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Management" :crumbs="['Home', 'ProductManagement', 'Sales']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Product Sales</h4></div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.product-sales.rebates')" class="btn btn-warning btn-sm">
                            <i class="fa fa-eye"></i> Rebates
                        </Link>
                        <Link v-if="can('productSales.create')" :href="route('inertia.product-sales.create')" class="btn btn-secondary btn-sm ms-1">
                            <i class="fa fa-plus"></i> Add Sale
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="sales.data"
                    :meta="paginatorMeta(sales)"
                    :search="table.state.search"
                    :sort="table.state.sort"
                    :direction="table.state.direction"
                    :loading="table.processing.value"
                    actions-label="Option"
                    @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                    @sort="(column) => table.toggleSort(column, listUrl)"
                    @page="(page) => { table.state.page = page; fetchList(); }"
                    @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                    @reset="table.reset(listUrl)"
                >
                    <template #actions="{ row }">
                        <Link v-if="can('productSales.view')" :href="route('inertia.product-sales.show', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-eye"></i> Detail
                        </Link>
                    </template>
                </DataTable>
            </div>
        </div>
    </div>
</template>
