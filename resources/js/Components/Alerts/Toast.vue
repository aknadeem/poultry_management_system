<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const flash = computed(() => page.props.flash ?? {});
const errors = computed(() => page.props.errors ?? {});
const errorList = computed(() => Object.values(errors.value).flat());

const alertClass = computed(() => {
    if (flash.value.type === 'error' || errorList.value.length) {
        return 'alert-danger';
    }

    if (flash.value.type === 'warning') {
        return 'alert-warning';
    }

    return 'alert-success';
});

const visible = computed(() => {
    return Boolean(flash.value.message || flash.value.status || flash.value.title || errorList.value.length);
});
</script>

<template>
    <div v-if="visible" class="alert" :class="alertClass" role="alert">
        <strong v-if="flash.title">{{ flash.title }}</strong>
        <div v-if="flash.message">{{ flash.message }}</div>
        <div v-else-if="flash.status">{{ flash.status }}</div>
        <ul v-if="errorList.length" class="mb-0">
            <li v-for="error in errorList" :key="error">{{ error }}</li>
        </ul>
    </div>
</template>
