<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import PaymentForm from '../../Components/CompanyBalances/PaymentForm.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

const route = useRoute();
const { can } = usePermissions();

defineProps({
    balance: { type: Object, required: true },
    payments: { type: Array, required: true },
});

const showPaymentModal = ref(false);
const reverseForm = useForm({
    reversal_reason: '',
});

function statusClass(status) {
    const value = String(status || '').toLowerCase();
    if (value === 'paid') {
        return 'bg-success';
    }
    if (value === 'unpaid') {
        return 'bg-danger';
    }

    return 'bg-warning';
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
    reverseForm.post(route('inertia.company-balances.payments.reverse', payment.id), {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['balance', 'payments'] }),
    });
}
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Balance Payments" :crumbs="['Home', 'BalancePayments']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <h4>Balance Payments</h4>
                        <h6 class="text-muted mb-0">{{ balance.company_name }}</h6>
                    </div>
                    <div class="col-6 text-end">
                        <a
                            v-if="balance.company_logo_url"
                            :href="balance.company_logo_url"
                            target="_blank"
                            class="me-2"
                            title="click to view"
                        >
                            <img :src="balance.company_logo_url" alt="Company Logo" style="width: 10%; max-width: 80px;">
                        </a>
                        <Link :href="route('inertia.company-balances.index')" class="btn btn-secondary btn-sm me-1">
                            <i class="fa fa-arrow-left"></i> Back
                        </Link>
                        <button
                            v-if="can('companyBalances.create') && Number(balance.remaining_amount) > 0"
                            type="button"
                            class="btn btn-primary btn-sm"
                            @click="showPaymentModal = true"
                        >
                            <i class="fa fa-money-bill"></i> Add Payment
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Company:</div>
                                    <div class="col-8">{{ balance.company_name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Type:</div>
                                    <div class="col-8">{{ balance.type }}</div>
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
                                        <span class="badge" :class="statusClass(balance.status)">
                                            {{ balance.status }}
                                        </span>
                                    </div>
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
                                        v-if="(payment.payment_status || 'posted') !== 'reversed' && can('companyBalances.update')"
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
            v-if="showPaymentModal"
            :show="showPaymentModal"
            :company-balance="balance"
            @close="onPaymentClose"
        />
    </div>
</template>
