<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    broker: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Brokers']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6"><h4>Broker Detail</h4></div>
                            <div class="col-6 text-end">
                                <Link v-if="can('parties.update')" :href="route('inertia.brokers.edit', broker)" class="btn btn-info btn-sm">
                                    <i class="fa fa-pencil-alt"></i> Edit
                                </Link>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-2"><label>Name</label><p class="mb-0">{{ broker.name }}</p></div>
                            <div class="col-sm-6 mb-2"><label>CNIC</label><p class="mb-0">{{ broker.cnic_no || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Contact Number</label><p class="mb-0">{{ broker.contact_number || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Email</label><p class="mb-0">{{ broker.email || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Province</label><p class="mb-0">{{ broker.province || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>City</label><p class="mb-0">{{ broker.city || '—' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
