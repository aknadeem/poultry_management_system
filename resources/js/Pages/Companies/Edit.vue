<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import CompanyForm from '../../Components/Companies/CompanyForm.vue';
import { useRoute } from '../../Utils/route';

const route = useRoute();

defineProps({
    company: { type: Object, required: true },
    businessTypes: { type: Array, default: () => [] },
});
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Companies', 'Edit']" />
        <div class="card">
            <div class="card-body">
                <CompanyForm
                    form-title="Edit Company"
                    :company="company"
                    :submit-url="route('inertia.companies.update', company)"
                    :business-types="businessTypes"
                    method="put"
                />
            </div>
        </div>
    </div>
</template>
