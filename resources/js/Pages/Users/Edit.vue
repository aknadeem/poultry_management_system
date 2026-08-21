<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import UserForm from '../../Components/Users/UserForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
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
                        <h4 class="header-title mb-3">Edit User</h4>
                        <UserForm
                            :user="user"
                            :roles="roles"
                            method="put"
                            :submit-url="route('inertia.users.update', props.user)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
