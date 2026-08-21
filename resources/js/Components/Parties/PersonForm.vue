<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import TextInput from '../Forms/TextInput.vue';
import LocationSelect from '../Forms/LocationSelect.vue';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';

const props = defineProps({
    person: {
        type: Object,
        default: null,
    },
    countries: {
        type: Array,
        default: () => [],
    },
    submitUrl: {
        type: String,
        required: true,
    },
    indexUrl: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'post',
    },
});

const form = useForm({
    name: props.person?.name ?? '',
    guardian_name: props.person?.guardian_name ?? '',
    cnic_no: props.person?.cnic_no ?? '',
    email: props.person?.email ?? '',
    contact_number: props.person?.contact_number ?? '',
    country_id: props.person?.country_id ?? '',
    province_id: props.person?.province_id ?? '',
    city_id: props.person?.city_id ?? '',
    address: props.person?.address ?? '',
    image_file: null,
});

function submit() {
    form.submit(props.method, props.submitUrl, {
        forceFormData: true,
    });
}

function onFileChange(event) {
    form.image_file = event.target.files[0] ?? null;
}
</script>

<template>
    <form autocomplete="off" @submit.prevent="submit">
        <div class="row form-group">
            <div class="col-sm-6 mb-2 pe-0">
                <TextInput id="name" v-model="form.name" label="Name *" placeholder="Enter name">
                    <FormError :message="form.errors.name" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2">
                <TextInput id="guardian_name" v-model="form.guardian_name" label="Father / Guardian Name *" placeholder="Enter guardian name">
                    <FormError :message="form.errors.guardian_name" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2 pe-0">
                <TextInput id="cnic_no" v-model="form.cnic_no" label="CNIC *" placeholder="13 digit CNIC">
                    <FormError :message="form.errors.cnic_no" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2">
                <TextInput id="contact_number" v-model="form.contact_number" label="Contact Number *" placeholder="11 digit contact number">
                    <FormError :message="form.errors.contact_number" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2 pe-0">
                <TextInput id="email" v-model="form.email" type="email" label="Email *" placeholder="Enter email">
                    <FormError :message="form.errors.email" />
                </TextInput>
            </div>
            <LocationSelect
                :countries="countries"
                :country-id="form.country_id"
                :province-id="form.province_id"
                :city-id="form.city_id"
                :errors="form.errors"
                @update:country-id="form.country_id = $event"
                @update:province-id="form.province_id = $event"
                @update:city-id="form.city_id = $event"
            />
            <div class="col-sm-12 mb-2">
                <TextInput id="address" v-model="form.address" label="Address" placeholder="Enter address">
                    <FormError :message="form.errors.address" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2">
                <label for="image_file">Image</label>
                <input id="image_file" class="form-control" type="file" accept="image/*" @change="onFileChange">
                <FormError :message="form.errors.image_file" />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-4 mb-3">
                <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                <Link :href="indexUrl" class="ms-1">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
            </div>
        </div>
    </form>
</template>
