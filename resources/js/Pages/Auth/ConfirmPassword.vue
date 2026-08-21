<script setup>
import { useForm } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const form = useForm({
    password: '',
});
const route = useRoute();

function submit() {
    form.post(route('inertia.password.confirm'));
}
</script>

<template>
    <form @submit.prevent="submit">
        <p class="text-muted">Please confirm your password before continuing.</p>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input id="password" v-model="form.password" class="form-control" type="password" autocomplete="current-password">
            <span v-if="form.errors.password" class="text-danger">{{ form.errors.password }}</span>
        </div>
        <div class="form-group mb-0 text-center">
            <button type="submit" class="btn btn-dark btn-block" :disabled="form.processing">Confirm Password</button>
        </div>
    </form>
</template>
