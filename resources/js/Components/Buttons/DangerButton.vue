<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button class="btn btn-danger" :type="type" :disabled="disabled">
        <slot />
    </button>
</template>
