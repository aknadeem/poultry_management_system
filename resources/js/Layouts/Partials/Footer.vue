<script setup>
import { usePage } from '@inertiajs/vue3';

const theme = usePage().props.theme ?? {};
</script>

<template>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    {{ theme.year }} &copy; Poultry Management System(PMS)
                </div>
            </div>
        </div>
    </footer>
</template>
