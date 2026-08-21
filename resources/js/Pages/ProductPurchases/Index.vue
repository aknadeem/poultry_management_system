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
    purchases: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.product-purchases.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.purchases.current_page,
    perPage: props.purchases.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'company_name', label: 'Company' },
    { key: 'category_name', label: 'Category' },
    { key: 'purchase_date_label', label: 'Date', sortable: true },
    { key: 'payment_status_label', label: 'Payment' },
    { key: 'is_active', label: 'Status' },
    { key: 'total_amount', label: 'Total Price', sortable: true },
    { key: 'discount_amount', label: 'Discount' },
    { key: 'other_charges', label: 'Tax' },
    { key: 'rebate_amount', label: 'Rebate Amount' },
    { key: 'final_amount', label: 'Final Price', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}

function toggleStatus(purchase) {
    router.put(route('inertia.product-purchases.toggle-status', purchase));
}

async function destroyPurchase(purchase) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: 'Are you sure you want to delete this purchase?',
    });

    if (confirmed) {
        router.delete(route('inertia.product-purchases.destroy', purchase));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Management" :crumbs="['Home', 'ProductManagement', 'Purchases']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Product Purchases</h4></div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.product-purchases.rebates')" class="btn btn-warning btn-sm">
                            <i class="fa fa-eye"></i> Rebates
                        </Link>
                        <Link v-if="can('productPurchases.create')" :href="route('inertia.product-purchases.create')" class="btn btn-secondary btn-sm ms-1">
                            <i class="fa fa-plus"></i> Add Purchase
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="purchases.data"
                    :meta="paginatorMeta(purchases)"
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
                    <template #cell.is_active="{ row }">
                        <a v-if="can('productPurchases.update')" href="javascript:void(0);" @click="toggleStatus(row)">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" :checked="row.is_active" readonly>
                            </div>
                        </a>
                    </template>
                    <template #actions="{ row }">
                        <Link v-if="can('productPurchases.view')" :href="route('inertia.product-purchases.show', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-eye"></i> Detail
                        </Link>
                        <a v-if="can('productPurchases.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyPurchase(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
