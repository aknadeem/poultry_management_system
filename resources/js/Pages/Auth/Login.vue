<script setup>
import { useForm } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
const route = useRoute();

function submit() {
    form.post(route('inertia.login'));
}
</script>

<template>
    <form class="needs-validation" novalidate @submit.prevent="submit">
        <div class="form-group mb-3">
            <label for="email">E-Mail Address</label>
            <input
                id="email"
                v-model="form.email"
                class="form-control"
                type="email"
                required
                autocomplete="email"
                autofocus
                placeholder="Enter your admission email"
            >
            <span v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
        </div>

        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input
                id="password"
                v-model="form.password"
                class="form-control"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Enter Your Password"
            >
            <span v-if="form.errors.password" class="text-danger">{{ form.errors.password }}</span>
        </div>

        <div class="form-group mb-0 text-center">
            <button type="submit" class="btn btn-dark btn-block" :disabled="form.processing">Login</button>
        </div>
    </form>
</template>
