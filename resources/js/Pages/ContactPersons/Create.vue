<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PersonForm from '../../Components/Parties/PersonForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    countries: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Contact Person', 'Create']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Add Contact Person</h4>
                        <PersonForm
                            :submit-url="route('inertia.conduct-persons.store')"
                            :index-url="route('inertia.conduct-persons.index')"
                            :countries="countries"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
