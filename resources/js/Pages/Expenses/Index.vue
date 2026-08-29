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
    expenses: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.expenses.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.expenses.current_page,
    perPage: props.expenses.per_page,
});
const confirm = useConfirm();

const columns = [
    { key: 'picture_url', label: 'Image' },
    { key: 'category_name', label: 'Category' },
    { key: 'expense_date', label: 'Expense Date', sortable: true },
    { key: 'amount', label: 'Amount', sortable: true },
    { key: 'remarks', label: 'Remarks' },
];

function fetchList() {
    table.fetch(listUrl);
}

async function destroyExpense(expense) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Expense: ${expense.id}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.expenses.destroy', expense));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Expense" :crumbs="['Home', 'ExpenseManagement']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6 align-self-start">
                        <h4>Expenses</h4>
                    </div>
                    <div class="col-6 align-self-end text-end mb-2">
                        <Link
                            v-if="can('expenses.create')"
                            :href="route('inertia.expenses.create')"
                            class="btn btn-secondary btn-sm"
                            title="Click to add new Expense"
                        >
                            <i class="fa fa-plus"></i> Expense
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="expenses.data"
                    :meta="paginatorMeta(expenses)"
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
                    <template #cell.picture_url="{ row }">
                        <img
                            v-if="row.picture_url"
                            class="rounded-circle avatar-lg"
                            :src="row.picture_url"
                            alt="Expense"
                            style="width: 48px; height: 48px; object-fit: cover;"
                        >
                        <b v-else>No Image</b>
                    </template>
                    <template #cell.category_name="{ row }">
                        <span>{{ row.category_name || '—' }}</span>
                    </template>
                    <template #cell.expense_date="{ row }">
                        {{ row.expense_date_label || row.expense_date || '—' }}
                    </template>
                    <template #cell.amount="{ row }">
                        <b>{{ row.amount }}</b>
                    </template>
                    <template #actions="{ row }">
                        <Link
                            v-if="can('expenses.view')"
                            :href="route('inertia.expenses.show', row)"
                            class="btn btn-secondary btn-sm"
                        >
                            <i class="fa fa-eye"></i> Detail
                        </Link>
                        <Link
                            v-if="can('expenses.update')"
                            :href="route('inertia.expenses.edit', row)"
                            class="btn btn-info btn-sm"
                            title="Click to edit"
                        >
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <a
                            v-if="can('expenses.delete')"
                            href="javascript:void(0);"
                            class="btn btn-danger btn-sm"
                            title="Click to delete"
                            @click="destroyExpense(row)"
                        >
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <ConfirmDialog
            :show="confirm.open.value"
            :title="confirm.title.value"
            :message="confirm.message.value"
            @confirm="confirm.confirm"
            @cancel="confirm.cancel"
        />
    </div>
</template>
