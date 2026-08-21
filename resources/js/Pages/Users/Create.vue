<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import UserForm from '../../Components/Users/UserForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    roles: {
        type: Array,
        required: true,
    },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Users" :crumbs="['Home', 'UserManagement', 'User']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Add User</h4>
                        <UserForm :submit-url="route('inertia.users.store')" :roles="roles" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
