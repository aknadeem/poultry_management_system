import { computed, reactive, watch } from 'vue';

const PAGE_SIZES = [10, 30, 40, -1];

export function useClientReportTable(rows, options = {}) {
    const state = reactive({
        page: 1,
        perPage: options.defaultPerPage ?? 10,
        columnFilters: { ...(options.initialFilters ?? {}) },
    });

    function matchesFilter(row, key, value) {
        if (value === '' || value === null || value === undefined) {
            return true;
        }

        if (value === 'all') {
            return true;
        }

        const rowValue = row[key];
        if (rowValue === null || rowValue === undefined) {
            return false;
        }

        if (options.exactMatchKeys?.includes(key)) {
            return String(rowValue) === String(value);
        }

        return String(rowValue).toLowerCase().includes(String(value).toLowerCase());
    }

    const filteredRows = computed(() => {
        const list = rows.value ?? rows;

        return list.filter((row) => Object.entries(state.columnFilters).every(([key, value]) => matchesFilter(row, key, value)));
    });

    const paginatedRows = computed(() => {
        if (state.perPage === -1) {
            return filteredRows.value;
        }

        const start = (state.page - 1) * state.perPage;

        return filteredRows.value.slice(start, start + state.perPage);
    });

    const meta = computed(() => {
        const total = filteredRows.value.length;
        const perPage = state.perPage === -1 ? total || 1 : state.perPage;
        const lastPage = state.perPage === -1 ? 1 : Math.max(1, Math.ceil(total / perPage) || 1);
        const page = Math.min(state.page, lastPage);
        const from = total === 0 ? 0 : ((page - 1) * perPage) + 1;
        const to = state.perPage === -1 ? total : Math.min(page * perPage, total);

        return {
            total,
            current_page: page,
            last_page: lastPage,
            per_page: state.perPage,
            from,
            to,
        };
    });

    watch(filteredRows, () => {
        if (state.page > meta.value.last_page) {
            state.page = meta.value.last_page;
        }
    });

    function setFilter(key, value) {
        state.columnFilters[key] = value;
        state.page = 1;
    }

    function setPage(page) {
        state.page = page;
    }

    function setPerPage(perPage) {
        state.perPage = perPage;
        state.page = 1;
    }

    function resetFilters() {
        Object.keys(state.columnFilters).forEach((key) => {
            state.columnFilters[key] = '';
        });
        state.page = 1;
    }

    return {
        state,
        pageSizes: PAGE_SIZES,
        filteredRows,
        paginatedRows,
        meta,
        setFilter,
        setPage,
        setPerPage,
        resetFilters,
    };
}
