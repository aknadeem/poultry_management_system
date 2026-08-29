<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { Link } from '@inertiajs/vue3';
import { useRoute } from '../../Utils/route';

const route = useRoute();

defineProps({
    company: { type: Object, required: true },
});
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Companies', 'Detail']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Company Detail</h4></div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.companies.index')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </Link>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Company Name</th>
                                    <td>{{ company.company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Company Code</th>
                                    <td>{{ company.company_code }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ company.company_address }}</td>
                                </tr>
                                <tr>
                                    <th>Business Type</th>
                                    <td>{{ company.business_type }}</td>
                                </tr>
                                <tr>
                                    <th>Vendor Info</th>
                                    <td>
                                        <div v-if="company.vendor_name">
                                            <strong>Name:</strong> {{ company.vendor_name }}<br>
                                            <strong>Contact:</strong> {{ company.contact_no }}<br>
                                            <strong>Email:</strong> {{ company.email }}
                                        </div>
                                        <div v-else class="text-muted">No associated vendor</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span v-if="company.is_active" class="badge bg-success">Active</span>
                                        <span v-else class="badge bg-danger">Inactive</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Added On</th>
                                    <td>{{ company.created_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 mb-3 text-center">
                        <div class="border p-2 rounded bg-light" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                            <img v-if="company.company_logo" :src="company.company_logo" alt="Logo" class="img-fluid" style="max-height: 180px;">
                            <div v-else class="text-muted text-center py-5">
                                <i class="fa fa-image fa-3x mb-2"></i>
                                <p>No Logo Available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
