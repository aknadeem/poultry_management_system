<script setup>
const props = defineProps({
    title: { type: String, required: true },
    headers: { type: Array, required: true },
    rows: { type: Array, required: true },
});

function cellValue(row, column) {
    const value = row[column.key];

    return value === null || value === undefined || value === '' ? '' : String(value);
}

function buildMatrix() {
    return [
        props.headers.map((column) => column.label),
        ...props.rows.map((row) => props.headers.map((column) => cellValue(row, column))),
    ];
}

async function copyReport() {
    const matrix = buildMatrix();
    const text = matrix.map((line) => line.join('\t')).join('\n');
    await navigator.clipboard.writeText(text);
}

function downloadCsv() {
    const matrix = buildMatrix();
    const csv = matrix
        .map((line) => line.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(','))
        .join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${props.title.replace(/\s+/g, '_').toLowerCase()}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);
}

function printReport() {
    const matrix = buildMatrix();
    const html = `
        <html>
            <head><title>${props.title}</title></head>
            <body>
                <h2>${props.title}</h2>
                <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;">
                    <thead>
                        <tr>${matrix[0].map((cell) => `<th>${cell}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${matrix.slice(1).map((line) => `<tr>${line.map((cell) => `<td>${cell}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
            </body>
        </html>
    `;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>

<template>
    <div class="mb-2">
        <button type="button" class="btn btn-secondary btn-sm me-1" @click="copyReport">Copy</button>
        <button type="button" class="btn btn-secondary btn-sm me-1" @click="downloadCsv">CSV</button>
        <button type="button" class="btn btn-secondary btn-sm" @click="printReport">Print</button>
    </div>
</template>
