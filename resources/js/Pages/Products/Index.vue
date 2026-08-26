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
import {ref} from "vue";

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.products.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.products.current_page,
    perPage: props.products.per_page,
});
const confirm = useConfirm();
const dialogVariant = ref('danger');
const columns = [
    { key: 'product_code', label: 'Product Code', sortable: true },
    { key: 'product_group_label', label: 'Product Group', sortable: true },
    { key: 'product_name', label: 'Product Name', sortable: true },
    { key: 'company_name', label: 'Company' },
    { key: 'category_name', label: 'Product Category' },
    { key: 'quantity', label: 'Quantity', sortable: true },
    { key: 'purchase_date', label: 'Purchase Date', sortable: true },
    { key: 'is_active', label: 'Status' },
];

function fetchList() {
    table.fetch(listUrl);
}

async function askToggleStatus(store) {
  dialogVariant.value = 'modern';
  const confirmed = await confirm.ask({
    title: 'Confirm Please?',
    message: 'Are you sure to continue?',
  });

  if (confirmed) {
    router.put(route('inertia.products.toggle-status', store));
  }
}

async function destroyProduct(product) {
  dialogVariant.value = 'danger';
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Product: ${product.product_name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.products.destroy', product));
    }
}

function toggleStatus(product) {
    router.put(route('inertia.products.toggle-status', product));
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Management" :crumbs="['Home', 'ProductManagement', 'Products']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Product List</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('products.create')" :href="route('inertia.products.create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Product
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="products.data"
                    :meta="paginatorMeta(products)"
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
                    <template #cell.product_group_label="{ row }">
                        <span class="badge text-white" :class="'bg-' + row.product_group_color">{{ row.product_group_label }}</span>
                    </template>

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
                        <Link v-if="can('products.view')" :href="route('inertia.products.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                        <Link v-if="can('products.update')" :href="route('inertia.products.edit', row)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <a v-if="can('products.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyProduct(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value"
                       :message="confirm.message.value"
                       :variant="dialogVariant"
                       @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
