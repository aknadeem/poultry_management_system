<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    party: {
        type: Object,
        required: true,
    },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Management" :crumbs="['Home', 'PartyManagement', 'Show']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 align-self-start">
                                <h4>Party Detail Data</h4>
                            </div>
                            <div class="col-6 align-self-end text-end mb-2">
                                <Link
                                    v-if="can('parties.update')"
                                    :href="route('inertia.parties.edit', party)"
                                    class="btn btn-info btn-sm"
                                >
                                    <i class="fa fa-pencil-alt"></i>
                                    Edit
                                </Link>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>Name</label>
                                <p class="mb-0">{{ party.name }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>CNIC</label>
                                <p class="mb-0">{{ party.cnic_no || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Contact Number</label>
                                <p class="mb-0">{{ party.contact_no || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Email</label>
                                <p class="mb-0">{{ party.email || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Farm</label>
                                <p class="mb-0">{{ party.farm_name || '—' }}</p>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <label>Company</label>
                                <p class="mb-0">{{ party.company_name || '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
