<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const route = useRoute();

const props = defineProps({
    company: { type: Object, default: null },
    businessTypes: { type: Array, default: () => [] },
    formTitle: { type: String, default: 'Add Company' },
    submitUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const isCreate = computed(() => props.company === null);
const imagePreview = ref(props.company?.company_logo ?? null);

const form = useForm({
    name: props.company?.company_name ?? '',
    contact_no: props.company?.contact_no ?? '',
    email: props.company?.email ?? '',
    address: props.company?.company_address ?? '',
    description: props.company?.description ?? '',
    business_type_id: props.company?.business_type_id ?? '',
    image_file: null,
});

function onFileChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.image_file = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

function submit() {
    form.submit(props.method, props.submitUrl, {
        forceFormData: true,
    });
}
</script>

<template>
    <div>
        <div class="row mb-2">
            <div class="col-6"><h4>{{ formTitle }}</h4></div>
            <div class="col-6 text-end">
                <Link :href="route('inertia.companies.index')" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Company Name *</label>
                    <input v-model="form.name" class="form-control" type="text" placeholder="Enter company name">
                    <FormError :message="form.errors.name" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Contact No *</label>
                    <input v-model="form.contact_no" class="form-control" type="text" placeholder="Enter contact number">
                    <FormError :message="form.errors.contact_no" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Email *</label>
                    <input v-model="form.email" class="form-control" type="email" placeholder="Enter email">
                    <FormError :message="form.errors.email" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Business Type</label>
                    <select v-model="form.business_type_id" class="form-select">
                        <option value="">-- Select Business Type --</option>
                        <option v-for="bt in businessTypes" :key="bt.id" :value="bt.id">{{ bt.name }}</option>
                    </select>
                    <FormError :message="form.errors.business_type_id" />
                </div>
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Address *</label>
                    <input v-model="form.address" class="form-control" type="text" placeholder="Enter address">
                    <FormError :message="form.errors.address" />
                </div>
                <div class="col-md-12 mb-3">
                    <label class="fw-bold">Description</label>
                    <textarea v-model="form.description" class="form-control" rows="2" placeholder="Enter description"></textarea>
                    <FormError :message="form.errors.description" />
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Company Logo</label>
                    <input class="form-control" type="file" accept="image/jpeg,image/jpg,image/png" @change="onFileChange">
                    <FormError :message="form.errors.image_file" />
                </div>
                <div v-if="imagePreview" class="col-md-6 mb-3 d-flex align-items-center">
                    <img :src="imagePreview" alt="Logo Preview" style="max-height: 80px; border-radius: 4px; border: 1px solid #ddd;">
                </div>
            </div>
            <div class="mt-3">
                <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                <Link :href="route('inertia.companies.index')" class="ms-1">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
            </div>
        </form>
    </div>
</template>
