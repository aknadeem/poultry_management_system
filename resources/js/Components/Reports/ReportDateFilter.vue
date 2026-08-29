<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    fromDate: { type: String, default: '' },
    toDate: { type: String, default: '' },
});

const emit = defineEmits(['search']);

function todayDate() {
    return new Date().toISOString().split('T')[0];
}

const fromDate = ref(props.fromDate || todayDate());
const toDate = ref(props.toDate || todayDate());

watch(() => props.fromDate, (value) => {
    if (value) {
        fromDate.value = value;
    }
});

watch(() => props.toDate, (value) => {
    if (value) {
        toDate.value = value;
    }
});

function submit() {
    emit('search', {
        from_date: fromDate.value,
        to_date: toDate.value,
    });
}
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-1">
                    <div class="row">
                        <div class="col-10 text-center">
                            <h4>Choose Dates:</h4>
                        </div>
                        <form autocomplete="off" @submit.prevent="submit">
                            <div class="row form-group mt-2">
                                <div class="col-2"></div>
                                <div class="col-sm-3 mb-2 fs-5">
                                    <label for="DateFrom" style="margin-bottom:2px;">Date From:</label>
                                    <input
                                        id="DateFrom"
                                        v-model="fromDate"
                                        type="date"
                                        class="form-control fs-5"
                                        required
                                    >
                                </div>
                                <div class="col-sm-3 mb-2 fs-5">
                                    <label for="DateTo" style="margin-bottom:2px;">Date To:</label>
                                    <input
                                        id="DateTo"
                                        v-model="toDate"
                                        type="date"
                                        class="form-control fs-5"
                                        required
                                    >
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-success btn-block mt-3">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
