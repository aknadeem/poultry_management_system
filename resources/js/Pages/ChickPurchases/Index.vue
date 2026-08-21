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
const listUrl = route('inertia.chick-purchases.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.purchases.current_page,
    perPage: props.purchases.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'picture', label: 'picture' },
    { key: 'company_name', label: 'Company' },
    { key: 'purchase_date', label: 'Purchase Date', sortable: true },
    { key: 'price', label: 'Price', sortable: true },
    { key: 'quantity', label: 'Quantity', sortable: true },
    { key: 'discount_amount', label: 'Dicount Amount', sortable: true },
    { key: 'total_price', label: 'total Price', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroyPurchase(purchase) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: 'Are you sure, you want to delete Chicken Purchase?',
    });

    if (confirmed) {
        router.delete(route('inertia.chick-purchases.destroy', purchase));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Chicken Purchase" :crumbs="['Home', 'Chickens']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Purchases</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('chickPurchases.create')" :href="route('inertia.chick-purchases.create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Create
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
                    <template #cell.company_name="{ row }">
                        <b>{{ row.company_name }}</b>
                    </template>
                    <template #cell.purchase_date="{ row }">
                        <b>{{ row.purchase_date_label }}</b>
                    </template>
                    <template #cell.total_price="{ row }">
                        <b>{{ row.total_price }}</b>
                    </template>
                    <template #actions="{ row }">
                        <Link v-if="can('chickPurchases.view')" :href="route('inertia.chick-purchases.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                        <Link v-if="can('chickPurchases.update')" :href="route('inertia.chick-purchases.edit', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <a v-if="can('chickPurchases.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyPurchase(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
