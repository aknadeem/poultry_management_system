<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';

const props = defineProps({
    store: { type: Object, default: null },
    formTitle: { type: String, default: 'Add Store Entry' },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const isCreate = computed(() => props.store === null);

const form = useForm({
    store_name: props.store?.store_name ?? '',
    store_type: props.store?.store_type ?? '',
    total_racks: props.store?.total_racks ?? '',
    store_area: props.store?.store_area ?? '',
    store_desciption: props.store?.description ?? '',
});

function submit() {
    form.submit(props.method, props.submitUrl);
}
</script>

<template>
    <div>
        <div class="row mb-2">
            <div class="col-6"><h4>{{ formTitle }}</h4></div>
            <div class="col-6 text-end">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Store Name *</label>
                    <input v-model="form.store_name" class="form-control" type="text" placeholder="Enter store name">
                    <FormError :message="form.errors.store_name" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Store Type *</label>
                    <input v-model="form.store_type" class="form-control" type="text" placeholder="Enter store type">
                    <FormError :message="form.errors.store_type" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Total Number of Racks *</label>
                    <input v-model="form.total_racks" class="form-control" type="number" min="0" placeholder="Enter total racks">
                    <FormError :message="form.errors.total_racks" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Store Area (sqft) *</label>
                    <input v-model="form.store_area" class="form-control" type="number" min="0" step="any" placeholder="Store area">
                    <FormError :message="form.errors.store_area" />
                </div>
                <div class="col-12 mb-3">
                    <label class="fw-bold">Description</label>
                    <textarea v-model="form.store_desciption" class="form-control" rows="3" placeholder="Enter Description"></textarea>
                    <FormError :message="form.errors.store_desciption" />
                </div>
            </div>
            <div class="mt-3">
                <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                <Link :href="indexUrl" class="ms-1">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
            </div>
        </form>
    </div>
</template>
