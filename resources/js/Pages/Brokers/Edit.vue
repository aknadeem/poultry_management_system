<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PersonForm from '../../Components/Parties/PersonForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    broker: { type: Object, required: true },
    countries: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Brokers', 'Edit']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Edit Broker</h4>
                        <PersonForm
                            method="put"
                            :person="broker"
                            :submit-url="route('inertia.brokers.update', props.broker)"
                            :index-url="route('inertia.brokers.index')"
                            :countries="countries"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
