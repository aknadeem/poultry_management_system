<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PartyForm from '../../Components/Parties/PartyForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    party: { type: Object, required: true },
    countries: { type: Array, default: () => [] },
    divisions: { type: Array, default: () => [] },
    customerTypes: { type: Array, default: () => [] },
    farmTypes: { type: Array, default: () => [] },
    farmSubtypes: { type: Array, default: () => [] },
    vendorTypes: { type: Array, default: () => [] },
    businessTypes: { type: Array, default: () => [] },
    contactPersons: { type: Array, default: () => [] },
    amountTypes: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Vendors" :crumbs="['Home', 'Vendor', 'Edit']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <PartyForm
                            mode="vendor"
                            form-title="Create Vendor"
                            method="put"
                            :party="party"
                            :submit-url="route('inertia.vendors.update', props.party)"
                            :index-url="route('inertia.vendors.index')"
                            :countries="countries"
                            :divisions="divisions"
                            :customer-types="customerTypes"
                            :farm-types="farmTypes"
                            :farm-subtypes="farmSubtypes"
                            :vendor-types="vendorTypes"
                            :business-types="businessTypes"
                            :contact-persons="contactPersons"
                            :amount-types="amountTypes"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
