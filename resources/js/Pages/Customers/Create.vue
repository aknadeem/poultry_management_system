<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PartyForm from '../../Components/Parties/PartyForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
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
        <PageTitle title="Customers" :crumbs="['Home', 'Customer', 'Create']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <PartyForm
                            mode="customer"
                            form-title="Create Customer"
                            :submit-url="route('inertia.customers.store')"
                            :index-url="route('inertia.customers.index')"
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
