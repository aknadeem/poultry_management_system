<script setup>
import { usePage } from '@inertiajs/vue3';
import Toast from '../Components/Alerts/Toast.vue';
import { useAuthBody } from '../Composables/useHyperLayout';

const page = usePage();
const theme = page.props.theme ?? {};

useAuthBody();
</script>

<template>
    <div>
        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <div class="auth-logo">
                                        <span class="logo logo-dark text-center">
                                            <span class="logo-lg">
                                                <img :src="theme.logo" alt="" height="100">
                                            </span>
                                        </span>
                                        <h4>{{ theme.appName || 'Poultry Management System' }}</h4>
                                    </div>
                                </div>
                                <Toast />
                                <slot />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer footer-alt text-white-50">
            {{ theme.year }} &copy; All rights reserved. Poultry Management System(PMS)
        </footer>
    </div>
</template>
