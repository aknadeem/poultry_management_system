<script setup>
defineProps({
    label: {
        type: String,
        default: 'Loading...',
    },
});
</script>

<template>
    <div class="d-flex align-items-center gap-2 text-muted">
        <div class="spinner-border spinner-border-sm" role="status">
            <span class="visually-hidden">{{ label }}</span>
        </div>
        <span>{{ label }}</span>
    </div>
</template>
