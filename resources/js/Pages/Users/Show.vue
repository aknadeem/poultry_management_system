<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Users" :crumbs="['Home', 'UserManagement', 'User']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>User Detail Data</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link
                                    v-if="can('users.update')"
                                    :href="route('inertia.users.edit', user)"
                                    class="btn btn-info btn-sm"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                    Edit
                                </Link>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>Name</label>
                                <p class="mb-0">{{ user.name }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Email</label>
                                <p class="mb-0">{{ user.email }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Contact Number</label>
                                <p class="mb-0">{{ user.contact_no || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>User Role</label>
                                <p class="mb-0">{{ user.role || '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
