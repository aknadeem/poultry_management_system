<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import ConfirmDialog from '../../Components/Modal/ConfirmDialog.vue';
import FormModal from '../../Components/Modal/FormModal.vue';
import FormError from '../../Components/Forms/FormError.vue';
import PrimaryButton from '../../Components/Buttons/PrimaryButton.vue';
import SecondaryButton from '../../Components/Buttons/SecondaryButton.vue';
import { useDataTable } from '../../Composables/useDataTable';
import { useConfirm } from '../../Composables/useConfirm';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';
import { paginatorMeta } from '../../Utils/pagination';

const props = defineProps({
    feeds: { type: Object, required: true },
    filters: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
});

const route = useRoute();
const { can } = usePermissions();
const listUrl = route('inertia.feeds.index');
const table = useDataTable({
    search: props.filters.search,
    sort: props.filters.sort,
    direction: props.filters.direction,
    page: props.feeds.current_page,
    perPage: props.feeds.per_page,
});
const confirm = useConfirm();
const editOpen = ref(false);
const editForm = useForm({
    feed_name: '',
    feed_category_id: '',
});
const editingFeedId = ref(null);
const columns = [
    { key: 'category_name', label: 'Category' },
    { key: 'feed_name', label: 'Feed Name', sortable: true },
    { key: 'total_quantity', label: 'Total Quantity', sortable: true },
    { key: 'remaining_quantity', label: 'Remaining Quantity', sortable: true },
];

function fetchList() {
    table.fetch(listUrl);
}

function openEdit(feed) {
    editingFeedId.value = feed.id;
    editForm.feed_name = feed.feed_name;
    editForm.feed_category_id = feed.feed_category_id;
    editForm.clearErrors();
    editOpen.value = true;
}

function submitEdit() {
    editForm.put(route('inertia.feeds.update', { id: editingFeedId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            editOpen.value = false;
        },
    });
}

async function destroyFeed(feed) {
    const confirmed = await confirm.ask({
        title: 'Please Confirm!',
        message: `Are you sure, you want to delete Feed ${feed.feed_name}?`,
    });

    if (confirmed) {
        router.delete(route('inertia.feeds.destroy', feed));
    }
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Feed Inventory" :crumbs="['Home', 'Feed']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Feed list</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('feeds.create')" :href="route('inertia.feeds.create')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-plus"></i> Feed
                        </Link>
                    </div>
                </div>
                <DataTable
                    :columns="columns"
                    :rows="feeds.data"
                    :meta="paginatorMeta(feeds)"
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
                    <template #cell.category_name="{ row }">
                        <b>{{ row.category_name }}</b>
                    </template>
                    <template #actions="{ row }">
                        <Link v-if="can('feeds.view')" :href="route('inertia.feeds.show', row)" class="btn btn-secondary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </Link>
                        <a v-if="can('feeds.update')" href="javascript:void(0);" class="btn btn-info btn-sm" @click="openEdit(row)">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </a>
                        <a v-if="can('feeds.delete')" href="javascript:void(0);" class="btn btn-danger btn-sm" @click="destroyFeed(row)">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    </template>
                </DataTable>
            </div>
        </div>
        <FormModal :show="editOpen" title="Update Feed" size="md" @close="editOpen = false">
            <form autocomplete="off" @submit.prevent="submitEdit">
                <div class="mb-2">
                    <label>Feed Name *</label>
                    <input v-model="editForm.feed_name" class="form-control" type="text">
                    <FormError :message="editForm.errors.feed_name" />
                </div>
                <div class="mb-2">
                    <label>Select category</label>
                    <select v-model="editForm.feed_category_id" class="form-control">
                        <option value="">Select category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                    <FormError :message="editForm.errors.feed_category_id" />
                </div>
                <PrimaryButton type="submit" :disabled="editForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="editOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
        <ConfirmDialog :show="confirm.open.value" :title="confirm.title.value" :message="confirm.message.value" @confirm="confirm.confirm" @cancel="confirm.cancel" />
    </div>
</template>
