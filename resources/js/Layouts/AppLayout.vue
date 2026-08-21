<script setup>
import Toast from '../Components/Alerts/Toast.vue';
import Topbar from './Partials/Topbar.vue';
import Topnav from './Partials/Topnav.vue';
import Footer from './Partials/Footer.vue';
import { useAppBody } from '../Composables/useHyperLayout';

useAppBody();
</script>

<template>
    <div>
        <div id="wrapper">
            <Topbar />
            <Topnav />
            <div class="content-page">
                <div class="content">
                    <Toast />
                    <slot />
                </div>
                <Footer />
            </div>
        </div>
        <div class="rightbar-overlay"></div>
    </div>
</template>

