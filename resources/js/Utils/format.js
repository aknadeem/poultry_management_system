export function formatCellValue(value, format) {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    if (format === 'date') {
        const date = new Date(value);

        return Number.isNaN(date.getTime()) ? String(value) : date.toLocaleDateString();
    }

    if (format === 'datetime') {
        const date = new Date(value);

        return Number.isNaN(date.getTime()) ? String(value) : date.toLocaleString();
    }

    if (format === 'number') {
        const amount = Number(value);

        return Number.isNaN(amount) ? String(value) : amount.toLocaleString();
    }

    return value;
}
