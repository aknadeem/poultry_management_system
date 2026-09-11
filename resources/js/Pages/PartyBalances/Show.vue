<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PaymentForm from '../../Components/PartyBalances/PaymentForm.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    balance: { type: Object, required: true },
    payments: { type: Array, default: () => [] },
    today: { type: String, default: '' },
});

const route = useRoute();
const { can } = usePermissions();
const showPaymentModal = ref(false);
const reverseForm = useForm({
    reversal_reason: '',
});

function statusClass(color) {
    return `bg-${color || 'secondary'}`;
}

function onPaymentClose() {
    showPaymentModal.value = false;
    router.reload({ only: ['balance', 'payments'] });
}

function reversePayment(payment) {
    if (payment.payment_status === 'reversed') {
        return;
    }

    reverseForm.reversal_reason = window.prompt('Optional reversal reason:') || '';
    reverseForm.post(route('inertia.party-balances.payments.reverse', payment.id), {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['balance', 'payments'] }),
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Party Balance" :crumbs="['Home', 'PartyManagement', 'Party Balance']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <h4>Balance Detail</h4>
                        <h6 class="text-muted mb-0">{{ balance.party_name }}</h6>
                    </div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.party-balances.index')" class="btn btn-secondary btn-sm me-1">
                            <i class="fa fa-arrow-left"></i> Back
                        </Link>
                        <button
                            v-if="can('partyBalances.create') && Number(balance.remaining_amount) > 0"
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="showPaymentModal = true"
                        >
                            <i class="fa fa-plus"></i> Add Payment
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Party:</div>
                                    <div class="col-8">{{ balance.party_name || '—' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">CNIC:</div>
                                    <div class="col-8">{{ balance.party_cnic || '—' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Type:</div>
                                    <div class="col-8">
                                        <span class="badge" :class="statusClass(balance.amount_type_color)">
                                            {{ balance.amount_type || '—' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Total:</div>
                                    <div class="col-8">{{ Number(balance.total_amount).toLocaleString() }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Paid:</div>
                                    <div class="col-8 text-success">{{ Number(balance.paid_amount).toLocaleString() }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Remaining:</div>
                                    <div class="col-8 text-danger fw-bold">{{ Number(balance.remaining_amount).toLocaleString() }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Status:</div>
                                    <div class="col-8">
                                        <span class="badge" :class="statusClass(balance.payment_status_color)">
                                            {{ balance.payment_status || '—' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Date:</div>
                                    <div class="col-8">{{ balance.transaction_date || '—' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Narration:</div>
                                    <div class="col-8">{{ balance.narration || '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped dt-responsive w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Paid Amount</th>
                                <th>Paid through</th>
                                <th>Status</th>
                                <th>Paid By</th>
                                <th>Paid At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="payments.length === 0">
                                <td colspan="7" class="text-center text-muted">No payments recorded.</td>
                            </tr>
                            <tr v-for="(payment, index) in payments" :key="payment.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ payment.paid_amount }}</td>
                                <td>{{ payment.payment_option_label || payment.payment_option }}</td>
                                <td>{{ payment.payment_status || 'posted' }}</td>
                                <td>{{ payment.added_by || '—' }}</td>
                                <td>{{ payment.created_at }}</td>
                                <td>
                                    <button
                                        v-if="(payment.payment_status || 'posted') !== 'reversed' && can('partyBalances.update')"
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        :disabled="reverseForm.processing"
                                        @click="reversePayment(payment)"
                                    >
                                        Reverse
                                    </button>
                                    <span v-else class="text-muted">{{ payment.reversal_reason || '—' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <PaymentForm
            v-if="balance"
            :show="showPaymentModal"
            :balance="balance"
            :today="today"
            @close="onPaymentClose"
        />
    </div>
</template>
