<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const route = useRoute();

function submit() {
    form.post(route('inertia.register'));
}
</script>

<template>
    <form class="needs-validation" novalidate @submit.prevent="submit">
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input id="name" v-model="form.name" class="form-control" type="text" autocomplete="name">
            <span v-if="form.errors.name" class="text-danger">{{ form.errors.name }}</span>
        </div>
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
            <button type="submit" class="btn btn-dark btn-block" :disabled="form.processing">Register</button>
        </div>
        <div class="text-center mt-3">
            <Link :href="route('inertia.login')">Already registered?</Link>
        </div>
    </form>
</template>
