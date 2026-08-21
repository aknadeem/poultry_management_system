import { router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

function cloneFilters(filters) {
    return { ...(filters ?? {}) };
}

export function useDataTable(defaults = {}) {
    const processing = ref(false);
    const initial = {
        search: defaults.search ?? '',
        sort: defaults.sort ?? '',
        direction: defaults.direction ?? 'desc',
        page: defaults.page ?? 1,
        perPage: defaults.perPage ?? 10,
        filters: cloneFilters(defaults.filters),
    };

    const state = reactive({
        search: initial.search,
        sort: initial.sort,
        direction: initial.direction,
        page: initial.page,
        perPage: initial.perPage,
        filters: cloneFilters(initial.filters),
    });

    function fetch(url, extra = {}) {
        router.get(url, {
            search: state.search,
            sort: state.sort,
            direction: state.direction,
            page: state.page,
            per_page: state.perPage,
            ...state.filters,
            ...extra,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => {
                processing.value = true;
            },
            onFinish: () => {
                processing.value = false;
            },
            onError: () => {
                processing.value = false;
            },
        });
    }

    function toggleSort(column, url) {
        if (state.sort === column) {
            state.direction = state.direction === 'asc' ? 'desc' : 'asc';
        } else {
            state.sort = column;
            state.direction = 'asc';
        }

        state.page = 1;
        fetch(url);
    }

    function reset(url) {
        state.search = initial.search;
        state.sort = initial.sort;
        state.direction = initial.direction;
        state.page = 1;
        state.perPage = initial.perPage;
        state.filters = cloneFilters(initial.filters);
        fetch(url);
    }

    return {
        state,
        processing,
        fetch,
        toggleSort,
        reset,
    };
}
