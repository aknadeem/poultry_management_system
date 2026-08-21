export function clampPerPage(value, allowed = [10, 25, 50], fallback = 10) {
    const size = Number(value);

    return allowed.includes(size) ? size : fallback;
}

export function paginatorMeta(paginator = {}) {
    return {
        current_page: Number(paginator.current_page ?? 1),
        last_page: Number(paginator.last_page ?? 1),
        per_page: Number(paginator.per_page ?? 10),
        total: Number(paginator.total ?? 0),
        from: paginator.from ?? null,
        to: paginator.to ?? null,
        links: Array.isArray(paginator.links) ? paginator.links : [],
        prev_page_url: paginator.prev_page_url ?? null,
        next_page_url: paginator.next_page_url ?? null,
    };
}

export function decodePaginatorLabel(label) {
    return String(label ?? '')
        .replace(/&laquo;/g, 'Previous')
        .replace(/&raquo;/g, 'Next')
        .replace(/&amp;/g, '&')
        .replace(/&hellip;/g, '...')
        .trim();
}

export function pageFromLink(link) {
    if (! link?.url) {
        return null;
    }

    try {
        const page = new URL(link.url, 'http://localhost').searchParams.get('page');

        return page ? Number(page) : 1;
    } catch {
        return null;
    }
}

export function compactPageWindow(current, last) {
    const currentPage = Number(current || 1);
    const lastPage = Number(last || 1);

    if (lastPage <= 7) {
        return Array.from({ length: lastPage }, (_, index) => index + 1);
    }

    const pages = new Set([1, 2, lastPage - 1, lastPage, currentPage - 1, currentPage, currentPage + 1]);

    return [...pages]
        .filter((page) => page >= 1 && page <= lastPage)
        .sort((a, b) => a - b);
}
