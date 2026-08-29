<script setup>
import { computed, toRef } from 'vue';
import { useClientReportTable } from '../../Composables/useClientReportTable';
import ReportExportButtons from './ReportExportButtons.vue';

const props = defineProps({
    title: { type: String, required: true },
    columns: { type: Array, required: true },
    rows: { type: Array, default: () => [] },
    showExport: { type: Boolean, default: true },
    exactMatchKeys: { type: Array, default: () => [] },
});

const table = useClientReportTable(toRef(props, 'rows'), {
    exactMatchKeys: props.exactMatchKeys,
});

const visibleColumns = computed(() => props.columns.filter((column) => ! column.hidden));
const exportColumns = computed(() => visibleColumns.value);

function rowNumber(index) {
    if (table.state.perPage === -1) {
        return index + 1;
    }

    return ((table.meta.value.current_page - 1) * table.state.perPage) + index + 1;
}
</script>

<template>
    <div class="row mt-0">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ReportExportButtons
                        v-if="showExport && rows.length"
                        :title="title"
                        :headers="exportColumns"
                        :rows="table.filteredRows.value"
                    />
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            Show
                            <select
                                class="form-select form-select-sm d-inline-block w-auto"
                                :value="table.state.perPage"
                                @change="table.setPerPage(Number($event.target.value))"
                            >
                                <option v-for="size in table.pageSizes" :key="size" :value="size">
                                    {{ size === -1 ? 'all' : size }}
                                </option>
                            </select>
                            entries
                        </div>
                        <div class="text-muted">
                            Showing {{ table.meta.value.from }} to {{ table.meta.value.to }} of {{ table.meta.value.total }} entries
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped dt-responsive w-100">
                            <thead>
                                <slot name="filters" :set-filter="table.setFilter" :filters="table.state.columnFilters" />
                                <tr>
                                    <th>#</th>
                                    <th v-for="column in visibleColumns" :key="column.key" :class="column.class">
                                        {{ column.label }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="table.paginatedRows.value.length === 0">
                                    <td :colspan="visibleColumns.length + 1" class="text-center text-muted py-4">
                                        No records found for the selected filters.
                                    </td>
                                </tr>
                                <tr v-for="(row, index) in table.paginatedRows.value" :key="row.id ?? index">
                                    <td>{{ rowNumber(index) }}</td>
                                    <td v-for="column in visibleColumns" :key="column.key" :class="column.class">
                                        <slot :name="`cell.${column.key}`" :row="row" :value="row[column.key]">
                                            <span v-if="column.html" v-html="row[column.key]"></span>
                                            <template v-else>{{ row[column.key] }}</template>
                                        </slot>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="table.meta.value.last_page > 1" class="d-flex justify-content-end mt-2">
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: table.meta.value.current_page <= 1 }">
                                    <button type="button" class="page-link" @click="table.setPage(table.meta.value.current_page - 1)">Previous</button>
                                </li>
                                <li
                                    v-for="page in table.meta.value.last_page"
                                    :key="page"
                                    class="page-item"
                                    :class="{ active: page === table.meta.value.current_page }"
                                >
                                    <button type="button" class="page-link" @click="table.setPage(page)">{{ page }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: table.meta.value.current_page >= table.meta.value.last_page }">
                                    <button type="button" class="page-link" @click="table.setPage(table.meta.value.current_page + 1)">Next</button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
