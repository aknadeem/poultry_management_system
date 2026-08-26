<script setup>
import { ref } from 'vue';
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
    stores: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.product-stores.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.stores.current_page,
    perPage: props.stores.per_page,
});
const confirmDialog = useConfirm();
const dialogVariant = ref('danger');
const columns = [
    { key: 'store_name', label: 'Store Name', sortable: true },
    { key: 'store_code', label: 'Store Code', sortable: true },
    { key: 'store_type', label: 'Store Type', sortable: true },
    { key: 'total_racks', label: 'Total Racks', sortable: true },
    { key: 'store_area', label: 'Store Area', sortable: true },
    { key: 'is_active', label: 'Status' },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroyStore(store) {
    dialogVariant.value = 'danger';
    const confirmed = await confirmDialog.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Store: ${store.store_name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.product-stores.destroy', store));
    }
}

async function askToggleStatus(store) {
    dialogVariant.value = 'modern';
    const confirmed = await confirmDialog.ask({
        title: 'Confirm Please?',
        message: 'Are you sure to continue?',
    });

    if (confirmed) {
        router.put(route('inertia.product-stores.toggle-status', store));
    }
}

function onConfirm() {
    confirmDialog.confirm();
}

function onCancel() {
    confirmDialog.cancel();
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Management" :crumbs="['Home', 'ProductManagement', 'Product Stores']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Store List</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('products.create')" :href="route('inertia.product-stores.create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Store
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="stores.data"
                    :meta="paginatorMeta(stores)"
                    :search="table.state.search"
                    :sort="table.state.sort"
                    :direction="table.state.direction"
                    :loading="table.processing.value"
                    actions-label="Options"
                    @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                    @sort="(column) => table.toggleSort(column, listUrl)"
                    @page="(page) => { table.state.page = page; fetchList(); }"
                    @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                    @reset="table.reset(listUrl)"
                >
                    <template #cell.is_active="{ row }">
                        <button
                            v-if="can('products.update')"
                            type="button"
                            class="btn btn-link p-0 border-0 align-baseline"
                            title="Click to update Status"
                            @click.prevent="askToggleStatus(row)"
                        >
                            <div class="form-check form-switch m-0">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :checked="Boolean(row.is_active)"
                                    tabindex="-1"
                                    @click.prevent
                                >
                            </div>
                        </button>
                        <div v-else class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" :checked="Boolean(row.is_active)" disabled>
                        </div>
                    </template>
                    <template #actions="{ row }">
                        <Link v-if="can('products.view')" :href="route('inertia.product-stores.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                        <Link v-if="can('products.update')" :href="route('inertia.product-stores.edit', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <a v-if="can('products.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyStore(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog
            :show="confirmDialog.open.value"
            :title="confirmDialog.title.value"
            :message="confirmDialog.message.value"
            :variant="dialogVariant"
            @confirm="onConfirm"
            @cancel="onCancel"
        />
    </div>
</template>
