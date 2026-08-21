<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PersonalFarmForm from '../../Components/Farms/PersonalFarmForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    countries: { type: Array, default: () => [] },
    farmTypes: { type: Array, default: () => [] },
    farmSubtypes: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Farm Management" :crumbs="['Home', 'FarmManagement', 'PersonalFarms', 'Create']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <PersonalFarmForm
                            form-title="Add New Farm"
                            :submit-url="route('inertia.personal-farms.store')"
                            :index-url="route('inertia.personal-farms.index')"
                            :countries="countries"
                            :farm-types="farmTypes"
                            :farm-subtypes="farmSubtypes"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
