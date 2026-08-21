<script setup>
import { computed } from 'vue';
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
    users: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.users.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.users.current_page,
    perPage: props.users.per_page,
    filters: {
        user_role_id: props.filters.user_role_id,
    },
});
const confirm = useConfirm();
const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'role', label: 'User Role' },
    { key: 'email', label: 'Email', sortable: true },
];
const hasFilters = computed(() => Boolean(table.state.filters.user_role_id));

function fetchList() {
    table.fetch(listUrl);
}

function onSearch(value) {
    table.state.search = value;
    table.state.page = 1;
    fetchList();
}

function onSort(column) {
    table.toggleSort(column, listUrl);
}

function onPage(page) {
    table.state.page = page;
    fetchList();
}

function onPageSize(size) {
    table.state.perPage = size;
    table.state.page = 1;
    fetchList();
}

function onRoleFilter(event) {
    table.state.filters.user_role_id = event.target.value;
    table.state.page = 1;
    fetchList();
}

function onReset() {
    table.reset(listUrl);
}

async function destroyUser(user) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete User: ${user.name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.users.destroy', user));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Users" :crumbs="['Home', 'UserManagement', 'User']" />

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Users</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link
                                    v-if="can('users.create')"
                                    :href="route('inertia.users.create')"
                                    class="btn btn-secondary btn-sm"
                                    title="Click to add new user"
                                >
                                    <i class="fa fa-plus"></i>
                                    User
                                </Link>
                            </div>
                        </div>

                        <div class="mb-2" style="max-width: 16rem">
                            <select class="form-control" :value="table.state.filters.user_role_id" @change="onRoleFilter">
                                <option value="">Select User Role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                        </div>

                        <DataTable
                            :columns="columns"
                            :rows="users.data"
                            :meta="paginatorMeta(users)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            :has-filters="hasFilters"
                            @update:search="onSearch"
                            @sort="onSort"
                            @page="onPage"
                            @page-size="onPageSize"
                            @reset="onReset"
                        >
                            <template #actions="{ row }">
                                <Link
                                    v-if="can('users.view')"
                                    :href="route('inertia.users.show', row)"
                                    class="btn btn-secondary btn-sm"
                                    title="View Details"
                                >
                                    <i class="fa fa-eye"></i>
                                    View
                                </Link>
                                <Link
                                    v-if="can('users.update')"
                                    :href="route('inertia.users.edit', row)"
                                    class="btn btn-info btn-sm"
                                    title="Click to edit"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                    Edit
                                </Link>
                                <a
                                    v-if="can('users.delete')"
                                    href="javascript:void(0);"
                                    class="btn btn-danger btn-sm"
                                    title="Click to delete"
                                    @click="destroyUser(row)"
                                >
                                    <i class="fa fa-trash"></i>
                                    Delete
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
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
