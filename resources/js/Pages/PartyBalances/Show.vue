<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    balance: { type: Object, required: true },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Balance" :crumbs="['Home', 'PartyManagement', 'Party Balance']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6"><h4>Balance Detail</h4></div>
                            <div class="col-6 text-end">
                                <Link :href="route('inertia.party-balances.index')" class="btn btn-secondary btn-sm">Back</Link>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 mb-2"><label>Party</label><p class="mb-0">{{ balance.party_name || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>CNIC</label><p class="mb-0">{{ balance.party_cnic || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Total Amount</label><p class="mb-0">{{ balance.total_amount }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Paid Amount</label><p class="mb-0">{{ balance.paid_amount }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Remaining</label><p class="mb-0">{{ balance.remaining_amount }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Type</label><p class="mb-0">{{ balance.amount_type || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Status</label><p class="mb-0">{{ balance.payment_status || '—' }}</p></div>
                            <div class="col-sm-6 mb-2"><label>Date</label><p class="mb-0">{{ balance.transaction_date || '—' }}</p></div>
                            <div class="col-sm-12 mb-2"><label>Narration</label><p class="mb-0">{{ balance.narration || '—' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
