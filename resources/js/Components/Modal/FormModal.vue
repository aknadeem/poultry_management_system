<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'lg',
    },
});

const emit = defineEmits(['close']);
</script>

<template>
    <div v-if="show" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog" :class="size === 'lg' ? 'modal-lg' : ''">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h4 class="modal-title">{{ title }}</h4>
                    <button type="button" class="btn-close" aria-label="Close" @click="emit('close')"></button>
                </div>
                <div class="modal-body">
                    <slot />
                </div>
            </div>
        </div>
    </div>
    <div v-if="show" class="modal-backdrop fade show"></div>
</template>
