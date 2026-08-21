<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    email: {
        type: String,
        default: '',
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});
const route = useRoute();

function submit() {
    form.post(route('inertia.password.update'));
}
</script>

<template>
    <form @submit.prevent="submit">
        <div class="form-group mb-3">
            <label for="email">E-Mail Address</label>
            <input id="email" v-model="form.email" class="form-control" type="email" autocomplete="email">
            <span v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input id="password" v-model="form.password" class="form-control" type="password" autocomplete="new-password">
            <span v-if="form.errors.password" class="text-danger">{{ form.errors.password }}</span>
        </div>
        <div class="form-group mb-3">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" v-model="form.password_confirmation" class="form-control" type="password" autocomplete="new-password">
        </div>
        <div class="form-group mb-0 text-center">
            <button type="submit" class="btn btn-dark btn-block" :disabled="form.processing">Reset Password</button>
        </div>
        <div class="text-center mt-3">
            <Link :href="route('inertia.login')">Back to login</Link>
        </div>
    </form>
</template>
