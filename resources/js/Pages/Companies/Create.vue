<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import CompanyForm from '../../Components/Companies/CompanyForm.vue';
import { useRoute } from '../../Utils/route';

const route = useRoute();

defineProps({
    businessTypes: { type: Array, default: () => [] },
});
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Companies', 'Create']" />
        <div class="card">
            <div class="card-body">
                <CompanyForm
                    form-title="Add Company"
                    :submit-url="route('inertia.companies.store')"
                    :business-types="businessTypes"
                    method="post"
                />
            </div>
        </div>
    </div>
</template>
