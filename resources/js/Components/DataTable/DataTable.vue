<script setup>
import { computed, onBeforeUnmount, ref, useSlots, watch } from 'vue';
import Spinner from '../Loading/Spinner.vue';
import { compactPageWindow, decodePaginatorLabel, pageFromLink } from '../../Utils/pagination';
import { formatCellValue } from '../../Utils/format';

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    rows: {
        type: Array,
        default: () => [],
    },
    meta: {
        type: Object,
        default: () => ({
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0,
            from: null,
            to: null,
            links: [],
        }),
    },
    search: {
        type: String,
        default: '',
    },
    sort: {
        type: String,
        default: '',
    },
    direction: {
        type: String,
        default: 'desc',
    },
    loading: {
        type: Boolean,
        default: false,
    },
    selectable: {
        type: Boolean,
        default: false,
    },
    selected: {
        type: Array,
        default: () => [],
    },
    debounceMs: {
        type: Number,
        default: 400,
    },
    rowKey: {
        type: String,
        default: 'id',
    },
    hasFilters: {
        type: Boolean,
        default: false,
    },
    emptyTitle: {
        type: String,
        default: 'No records found',
    },
    emptyMessage: {
        type: String,
        default: 'Try a different search or clear the current filters.',
    },
});

const emit = defineEmits(['update:search', 'sort', 'page', 'page-size', 'reset', 'update:selected']);
const slots = useSlots();
const localSearch = ref(props.search);
let searchTimer = null;

watch(() => props.search, (value) => {
    if (value !== localSearch.value) {
        localSearch.value = value;
    }
});

watch(localSearch, (value) => {
    if (value === props.search) {
        return;
    }

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        emit('update:search', value);
    }, props.debounceMs);
});

onBeforeUnmount(() => {
    clearTimeout(searchTimer);
});

const tableColumns = computed(() => props.columns.filter((column) => column.key !== 'actions'));
const showActions = computed(() => Boolean(slots.actions));
const paginatorLinks = computed(() => (Array.isArray(props.meta.links) ? props.meta.links : []));
const currentPage = computed(() => Number(props.meta.current_page ?? 1));
const lastPage = computed(() => Number(props.meta.last_page ?? 1));
const selectedKeys = computed(() => props.selected.map((value) => String(value)));
const rowIds = computed(() => props.rows.map((row) => row[props.rowKey]).filter((id) => id !== undefined && id !== null));
const allSelected = computed(() => rowIds.value.length > 0 && rowIds.value.every((id) => selectedKeys.value.includes(String(id))));
const hasActiveQuery = computed(() => Boolean(props.search) || props.hasFilters);
const colSpan = computed(() => {
    return tableColumns.value.length + (props.selectable ? 1 : 0) + (showActions.value ? 1 : 0);
});

const fallbackLinks = computed(() => {
    if (paginatorLinks.value.length) {
        return paginatorLinks.value;
    }

    const pages = compactPageWindow(currentPage.value, lastPage.value);
    const links = [{
        url: currentPage.value > 1 ? String(currentPage.value - 1) : null,
        label: 'Previous',
        active: false,
        page: currentPage.value > 1 ? currentPage.value - 1 : null,
    }];

    let previous = 0;
    pages.forEach((page) => {
        if (previous && page - previous > 1) {
            links.push({ url: null, label: '...', active: false, page: null });
        }

        links.push({
            url: String(page),
            label: String(page),
            active: page === currentPage.value,
            page,
        });
        previous = page;
    });

    links.push({
        url: currentPage.value < lastPage.value ? String(currentPage.value + 1) : null,
        label: 'Next',
        active: false,
        page: currentPage.value < lastPage.value ? currentPage.value + 1 : null,
    });

    return links;
});

function headerLabel(column) {
    return column.label ?? column.key;
}

function columnClass(column) {
    const align = column.align ?? 'left';

    return {
        'text-start': align === 'left',
        'text-center': align === 'center',
        'text-end': align === 'right',
        'user-select-none': column.sortable,
    };
}

function displayValue(column, row) {
    return formatCellValue(row[column.key], column.format);
}

function linkLabel(link) {
    return decodePaginatorLabel(link.label);
}

function isEllipsis(link) {
    return ! link.url && String(link.label).includes('...');
}

function isPageNumber(link) {
    return ! isPrevNext(link) && ! isEllipsis(link);
}

function isPrevNext(link) {
    const label = linkLabel(link).toLowerCase();

    return label.includes('previous') || label.includes('next');
}

function resolvePage(link) {
    if (link.page) {
        return Number(link.page);
    }

    return pageFromLink(link);
}

function onPageClick(link) {
    if (props.loading || ! link.url || link.active) {
        return;
    }

    const page = resolvePage(link);
    if (page) {
        emit('page', page);
    }
}

function isRowSelected(row) {
    return selectedKeys.value.includes(String(row[props.rowKey]));
}

function toggleRow(row) {
    const key = String(row[props.rowKey]);
    const next = isRowSelected(row)
        ? selectedKeys.value.filter((value) => value !== key)
        : [...selectedKeys.value, key];

    emit('update:selected', next);
}

function toggleAll() {
    if (allSelected.value) {
        emit('update:selected', []);

        return;
    }

    emit('update:selected', rowIds.value.map((id) => String(id)));
}
</script>

<template>
    <div class="datatable" :aria-busy="loading">
        <div class="row mb-2">
            <div class="col-sm-12 col-md-6">
                <label class="d-inline-flex align-items-center gap-2">
                    Show
                    <select
                        class="form-control form-control-sm"
                        style="width: auto"
                        :value="meta.per_page"
                        :disabled="loading"
                        @change="emit('page-size', Number($event.target.value))"
                    >
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                    </select>
                    entries
                </label>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="d-flex justify-content-md-end align-items-center gap-2">
                    <button
                        v-if="hasActiveQuery"
                        type="button"
                        class="btn btn-light btn-sm"
                        :disabled="loading"
                        @click="emit('reset')"
                    >
                        Reset
                    </button>
                    <label class="d-inline-flex align-items-center gap-2 mb-0">
                        Search:
                        <input
                            v-model="localSearch"
                            class="form-control form-control-sm"
                            type="search"
                            :disabled="loading"
                        >
                    </label>
                </div>
            </div>
        </div>

        <div class="position-relative">
            <div v-if="loading" class="datatable-overlay">
                <Spinner label="Loading..." />
            </div>

            <div class="table-responsive">
                <table class="table table-striped dt-responsive w-100 mb-0">
                    <thead>
                        <tr>
                            <th v-if="selectable" class="text-center" style="width: 2.5rem">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :checked="allSelected"
                                    :disabled="loading || ! rows.length"
                                    @change="toggleAll"
                                >
                            </th>
                            <th
                                v-for="column in tableColumns"
                                :key="column.key"
                                :class="columnClass(column)"
                                @click="! loading && column.sortable && emit('sort', column.key)"
                            >
                                {{ headerLabel(column) }}
                                <span v-if="sort === column.key">
                                    {{ direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th v-if="showActions" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="! rows.length">
                            <td :colspan="colSpan" class="text-center py-5">
                                <i class="dripicons-document h2 text-muted d-block"></i>
                                <h5 class="mt-2 mb-1">{{ emptyTitle }}</h5>
                                <p class="text-muted mb-3">{{ emptyMessage }}</p>
                                <button
                                    v-if="hasActiveQuery"
                                    type="button"
                                    class="btn btn-secondary btn-sm"
                                    @click="emit('reset')"
                                >
                                    Clear filters
                                </button>
                            </td>
                        </tr>
                        <tr v-for="(row, index) in rows" :key="row[rowKey] ?? index">
                            <td v-if="selectable" class="text-center">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :checked="isRowSelected(row)"
                                    :disabled="loading"
                                    @change="toggleRow(row)"
                                >
                            </td>
                            <td
                                v-for="column in tableColumns"
                                :key="column.key"
                                :class="columnClass(column)"
                            >
                                <slot :name="`cell.${column.key}`" :row="row">
                                    {{ displayValue(column, row) }}
                                </slot>
                            </td>
                            <td v-if="showActions" class="text-end">
                                <slot name="actions" :row="row" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-3">
            <small class="text-muted">
                <template v-if="meta.total">
                    Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} records
                </template>
                <template v-else>
                    0 records
                </template>
            </small>

            <ul v-if="lastPage > 1" class="pagination pagination-rounded mb-0">
                <li
                    v-for="(link, index) in fallbackLinks"
                    :key="`${link.label}-${index}`"
                    class="page-item"
                    :class="{
                        active: link.active,
                        disabled: loading || ! link.url,
                        'd-none d-md-block': isPageNumber(link) && ! link.active,
                    }"
                >
                    <span v-if="isEllipsis(link)" class="page-link">…</span>
                    <button
                        v-else
                        type="button"
                        class="page-link"
                        :disabled="loading || ! link.url"
                        @click="onPageClick(link)"
                    >
                        {{ linkLabel(link) }}
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.datatable-overlay {
    position: absolute;
    inset: 0;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.7);
}

@media (max-width: 767.98px) {
    .pagination .page-item.d-none {
        display: none !important;
    }
}
</style>
