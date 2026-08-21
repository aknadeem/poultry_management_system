import { usePage } from '@inertiajs/vue3';

export function resolveRoute(routes, name, params = {}) {
    const pattern = routes?.[name];

    if (! pattern) {
        throw new Error(`Unknown Inertia route: ${name}`);
    }

    const values = params !== null && typeof params === 'object' && ! Array.isArray(params)
        ? params
        : { id: params };

    const replacements = {
        __id__: values.user?.id ?? values.user ?? values.id,
        __token__: values.token,
    };

    let url = String(pattern);

    Object.entries(replacements).forEach(([placeholder, value]) => {
        if (value !== undefined && value !== null) {
            url = url.replaceAll(placeholder, String(value));
        }
    });

    return url;
}

export function useRoute() {
    const page = usePage();

    return (name, params = {}) => resolveRoute(page.props.routes, name, params);
}
