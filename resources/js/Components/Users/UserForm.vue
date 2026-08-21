<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import TextInput from '../Forms/TextInput.vue';
import SelectInput from '../Forms/SelectInput.vue';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    roles: {
        type: Array,
        required: true,
    },
    submitUrl: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'post',
    },
});

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    contact_no: props.user?.contact_no ?? '',
    user_role_id: props.user?.user_role_id ?? '',
    password: '',
    image_file: null,
});

const roleOptions = props.roles.map((role) => ({
    value: role.id,
    label: role.name,
}));
const route = useRoute();
const passwordLabel = computed(() => (props.user ? 'Password' : 'Password *'));

function submit() {
    form.transform((data) => ({
        ...data,
        user_role_id: data.user_role_id === '' ? null : Number(data.user_role_id),
    })).submit(props.method, props.submitUrl, {
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
                <TextInput id="name" v-model="form.name" label="Name *" placeholder="Enter User name">
                    <FormError :message="form.errors.name" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2">
                <TextInput id="contact_no" v-model="form.contact_no" label="Contact Number *" placeholder="Enter contact number">
                    <FormError :message="form.errors.contact_no" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2 pe-0">
                <TextInput id="email" v-model="form.email" type="email" label="Email *" placeholder="Enter email">
                    <FormError :message="form.errors.email" />
                </TextInput>
            </div>
            <div class="col-sm-6 mb-2">
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    :label="passwordLabel"
                    placeholder="Enter password"
                    autocomplete="new-password"
                >
                    <FormError :message="form.errors.password" />
                    <span v-if="user" class="text-muted">Leave blank to keep the current password.</span>
                </TextInput>
            </div>
            <div class="col-6 pe-0">
                <SelectInput
                    id="user_role_id"
                    v-model="form.user_role_id"
                    label="Select User Role*"
                    placeholder="Select User Role"
                    :options="roleOptions"
                >
                    <FormError :message="form.errors.user_role_id" />
                </SelectInput>
            </div>
            <div class="col-sm-6">
                <label for="image_file">Image</label>
                <input id="image_file" class="form-control" type="file" name="image_file" accept="image/*" @change="onFileChange">
                <FormError :message="form.errors.image_file" />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-4 mb-3">
                <PrimaryButton type="submit" :disabled="form.processing">Submit</PrimaryButton>
                <Link :href="route('inertia.users.index')" class="ms-1">
                    <SecondaryButton type="button">Cancel</SecondaryButton>
                </Link>
            </div>
        </div>
    </form>
</template>
