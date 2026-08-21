<script setup>
import { computed } from 'vue';
import PageTitle from '../../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../../Components/DataTable/DataTable.vue';
import { useDataTable } from '../../../Composables/useDataTable';
import { useRoute } from '../../../Utils/route';
import { paginatorMeta } from '../../../Utils/pagination';

const props = defineProps({
    roles: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const route = useRoute();
const listUrl = route('inertia.user-roles.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.roles.current_page,
    perPage: props.roles.per_page,
});
const columns = [
    { key: 'id', label: '#', sortable: true, align: 'center' },
    { key: 'name', label: 'Name', sortable: true },
    { key: 'slug', label: 'Slug', sortable: true },
];
const hasFilters = computed(() => false);

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
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="User Roles" :crumbs="['Home', 'UserManagement', 'User Role']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>User Role</h4>
                            </div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="roles.data"
                            :meta="paginatorMeta(roles)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            :has-filters="hasFilters"
                            @update:search="onSearch"
                            @sort="onSort"
                            @page="onPage"
                            @page-size="onPageSize"
                            @reset="table.reset(listUrl)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
