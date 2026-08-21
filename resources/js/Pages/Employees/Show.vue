<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    employee: { type: Object, required: true },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Employee Detail" :crumbs="['Home', 'Employee', 'Show']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Employee Detail Data</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link :href="route('inertia.employees.edit', employee)" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </Link>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>Name</label>
                                <p class="mb-0">{{ employee.name }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>CNIC</label>
                                <p class="mb-0">{{ employee.cnic_no || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Contact Number</label>
                                <p class="mb-0">{{ employee.contact_no || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Email</label>
                                <p class="mb-0">{{ employee.email || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Farm</label>
                                <p class="mb-0">{{ employee.farm_name || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Address</label>
                                <p class="mb-0">{{ employee.address || '—' }}</p>
                            </div>
                            <div v-if="employee.employee_image_url" class="col-sm-6 mb-2">
                                <label>Profile Image</label>
                                <p class="mb-0">
                                    <img :src="employee.employee_image_url" alt="Employee" class="rounded-circle avatar-lg">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
