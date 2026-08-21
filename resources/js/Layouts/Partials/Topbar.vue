<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const page = usePage();
const theme = page.props.theme ?? {};
const route = useRoute();
const userName = page.props.auth?.user?.name ?? 'Admin';

function logout() {
    router.post(route('inertia.logout'));
}
</script>

<template>
    <div class="navbar-custom">
        <div class="container-fluid">
            <ul class="list-unstyled topnav-menu float-end mb-0">
                <li class="dropdown d-none d-lg-inline-block">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#">
                        <i class="fe-maximize noti-icon"></i>
                    </a>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a
                        class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false"
                    >
                        <img :src="theme.logo" alt="logo" class="rounded-circle">
                        <span class="pro-user-name ms-1">
                            {{ userName }} <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>My Account</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-settings"></i>
                            <span>Settings</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-lock"></i>
                            <span>Lock Screen</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item notify-item" @click.prevent="logout">
                            <i class="fe-log-out"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </li>
            </ul>

            <div class="logo-box">
                <a :href="route('inertia.dashboard')" class="logo logo-dark text-center">
                    <span class="logo-sm">
                        <img :src="theme.logo" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img :src="theme.logo" alt="" height="20">
                    </span>
                </a>
                <a :href="route('inertia.dashboard')" class="logo logo-light text-center">
                    <span class="logo-sm">
                        <img :src="theme.logo" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img :src="theme.logo" alt="" height="70">
                    </span>
                </a>
            </div>

            <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                <li>
                    <button class="button-menu-mobile waves-effect waves-light" type="button">
                        <i class="fe-menu"></i>
                    </button>
                </li>
                <li>
                    <a class="navbar-toggle nav-link" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
</template>
