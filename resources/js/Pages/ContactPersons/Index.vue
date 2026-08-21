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
    people: { type: Object, required: true },
    filters: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.conduct-persons.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.people.current_page,
    perPage: props.people.per_page,
});
const confirm = useConfirm();
const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'guardian_name', label: 'Father / Guardian Name' },
    { key: 'cnic_no', label: 'CNIC', sortable: true },
    { key: 'contact_number', label: 'Contact Number', sortable: true },
    { key: 'province', label: 'Province' },
    { key: 'city', label: 'City' },
];
const hasFilters = computed(() => false);

function fetchList() {
    table.fetch(listUrl);
}

async function destroyPerson(person) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Contact Person: ${person.name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.conduct-persons.destroy', person));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Contact Person', 'index']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start"><h4>Contact Persons</h4></div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link v-if="can('parties.create')" :href="route('inertia.conduct-persons.create')" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-plus"></i> Contact Person
                                </Link>
                            </div>
                        </div>
                        <DataTable
                            :columns="columns"
                            :rows="people.data"
                            :meta="paginatorMeta(people)"
                            :search="table.state.search"
                            :sort="table.state.sort"
                            :direction="table.state.direction"
                            :loading="table.processing.value"
                            :has-filters="hasFilters"
                            @update:search="(value) => { table.state.search = value; table.state.page = 1; fetchList(); }"
                            @sort="(column) => table.toggleSort(column, listUrl)"
                            @page="(page) => { table.state.page = page; fetchList(); }"
                            @page-size="(size) => { table.state.perPage = size; table.state.page = 1; fetchList(); }"
                            @reset="table.reset(listUrl)"
                        >
                            <template #actions="{ row }">
                                <Link v-if="can('parties.view')" :href="route('inertia.conduct-persons.show', row)" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-eye"></i> View
                                </Link>
                                <Link v-if="can('parties.update')" :href="route('inertia.conduct-persons.edit', row)" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </Link>
                                <a v-if="can('parties.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyPerson(row)">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
