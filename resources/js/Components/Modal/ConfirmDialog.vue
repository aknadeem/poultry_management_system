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
});

const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
    <div v-if="show" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content modal-filled bg-danger">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-exit h1 text-white"></i>
                        <h4 class="mt-2 text-white">{{ title }}</h4>
                        <p class="mt-3 text-white">{{ message }}</p>
                        <button type="button" class="btn btn-dark my-2" @click="emit('confirm')">Yes</button>
                        <button type="button" class="btn btn-light my-2" @click="emit('cancel')">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="show" class="modal-backdrop fade show"></div>
</template>
