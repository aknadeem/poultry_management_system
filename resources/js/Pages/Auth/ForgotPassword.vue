<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const form = useForm({
    email: '',
});
const route = useRoute();

function submit() {
    form.post(route('inertia.password.email'));
}
</script>

<template>
    <form @submit.prevent="submit">
        <p class="text-muted">Enter your email and we will send a password reset link.</p>
        <div class="form-group mb-3">
            <label for="email">E-Mail Address</label>
            <input id="email" v-model="form.email" class="form-control" type="email" autocomplete="email">
            <span v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
        </div>
        <div class="form-group mb-0 text-center">
            <button type="submit" class="btn btn-dark btn-block" :disabled="form.processing">Send Password Reset Link</button>
        </div>
        <div class="text-center mt-3">
            <Link :href="route('inertia.login')">Back to login</Link>
        </div>
    </form>
</template>
