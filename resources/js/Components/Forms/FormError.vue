<script setup>
defineProps({
    message: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <span v-if="message" class="text-danger">{{ message }}</span>
</template>
