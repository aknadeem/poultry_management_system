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
    <button class="btn btn-secondary btn-sm waves-effect waves-light" :type="type" :disabled="disabled">
        <slot />
    </button>
</template>
