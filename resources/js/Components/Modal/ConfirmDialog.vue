<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Please Confirm!',
    },
    message: {
        type: String,
        default: 'Are you sure?',
    },
    /**
     * danger = red filled (delete). modern = white Blade jquery-confirm style (status).
     */
    variant: {
        type: String,
        default: 'danger',
    },
    confirmLabel: {
        type: String,
        default: '',
    },
    cancelLabel: {
        type: String,
        default: 'No',
    },
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="modal-backdrop fade show" style="z-index: 1050;"></div>
        <div
            v-if="show"
            class="modal fade show d-block"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            style="z-index: 1055;"
        >
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div
                    class="modal-content"
                    :class="variant === 'modern' ? 'border-0 shadow' : 'modal-filled bg-danger'"
                >
                    <div class="modal-body p-4">
                        <div class="text-center">
                            <template v-if="variant === 'modern'">
                                <h4 class="mt-2">{{ title }}</h4>
                                <p class="mt-3 text-muted mb-3">{{ message }}</p>
                                <button
                                    type="button"
                                    class="btn btn-info btn-sm me-1"
                                    @click.stop="emit('confirm')"
                                >
                                    {{ confirmLabel || 'Yes!' }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-light btn-sm"
                                    @click.stop="emit('cancel')"
                                >
                                    {{ cancelLabel }}
                                </button>
                            </template>
                            <template v-else>
                                <i class="dripicons-exit h1 text-white"></i>
                                <h4 class="mt-2 text-white">{{ title }}</h4>
                                <p class="mt-3 text-white">{{ message }}</p>
                                <button
                                    type="button"
                                    class="btn btn-dark my-2 me-1"
                                    @click.stop="emit('confirm')"
                                >
                                    {{ confirmLabel || 'Yes' }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-light my-2"
                                    @click.stop="emit('cancel')"
                                >
                                    {{ cancelLabel }}
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
