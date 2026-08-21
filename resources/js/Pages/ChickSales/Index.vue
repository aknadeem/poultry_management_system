<script setup>
import { Link, router } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import ConfirmDialog from '../../Components/Modal/ConfirmDialog.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useConfirm } from '../../Composables/useConfirm';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    sales: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.chick-sales.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.sales.current_page,
    perPage: props.sales.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'picture', label: 'picture' },
    { key: 'customer_name', label: 'Customer' },
    { key: 'sale_date', label: 'Sale Date', sortable: true },
    { key: 'per_kg_price', label: 'PerKgPrice', sortable: true },
    { key: 'total_weight', label: 'TotalWeight', sortable: true },
    { key: 'discount_amount', label: 'Dicount Amount', sortable: true },
    { key: 'total_price', label: 'total Price', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroySale(sale) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Sale id: ${sale.id}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.chick-sales.destroy', sale));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicken Sales" :crumbs="['Home', 'Chickens']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Sales</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('chickSales.create')" :href="route('inertia.chick-sales.create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Create
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
                    actions-label="Actions"
                    @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                    @sort="(column) => table.toggleSort(column, listUrl)"
                    @page="(page) => { table.state.page = page; fetchList(); }"
                    @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                    @reset="table.reset(listUrl)"
                >
                    <template #cell.picture="{ row }">
                        <img v-if="row.picture_url" class="rounded-circle avatar-lg" :src="row.picture_url" alt="No image">
                    </template>
                    <template #cell.sale_date="{ row }">
                        {{ row.sale_date_label }}
                    </template>
                    <template #actions="{ row }">
                        <Link v-if="can('chickSales.view')" :href="route('inertia.chick-sales.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                        <Link v-if="can('chickSales.update')" :href="route('inertia.chick-sales.edit', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <a v-if="can('chickSales.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroySale(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
