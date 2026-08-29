<script setup>
import { ref } from 'vue';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { Link } from '@inertiajs/vue3';
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
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Balance Management" :crumbs="['Home', 'BalanceManagement', 'Company Balances', 'Detail']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6"><h4>Balance Detail</h4></div>
                    <div class="col-6 text-end">
                        <Link :href="route('inertia.company-balances.index')" class="btn btn-secondary btn-sm me-2">
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

                <div class="row">
                    <!-- Balance Summary -->
                    <div class="col-md-5">
                        <div class="card bg-light">
                            <div class="card-body container-fluid">
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
                                    <div class="col-8">Rs {{ Number(balance.total_amount).toLocaleString() }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Paid:</div>
                                    <div class="col-8 text-success">Rs {{ Number(balance.paid_amount).toLocaleString() }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Remaining:</div>
                                    <div class="col-8 text-danger fw-bold">Rs {{ Number(balance.remaining_amount).toLocaleString() }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Status:</div>
                                    <div class="col-8">
                                        <span class="badge" :class="balance.status === 'Paid' ? 'bg-success' : (balance.status === 'Unpaid' ? 'bg-danger' : 'bg-warning')">
                                            {{ balance.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="col-md-7">
                        <h5 class="mb-3">Payment History</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="payments.length === 0">
                                        <td colspan="4" class="text-center text-muted">No payments recorded.</td>
                                    </tr>
                                    <tr v-for="payment in payments" :key="payment.id">
                                        <td>{{ payment.created_at }}</td>
                                        <td class="text-success fw-bold">Rs {{ Number(payment.paid_amount).toLocaleString() }}</td>
                                        <td>
                                            <span v-if="payment.payment_option == 1" class="badge bg-primary">Cash</span>
                                            <span v-if="payment.payment_option == 2" class="badge bg-info">Cheque</span>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85em;">
                                                <div v-if="payment.payment_option == 2">
                                                    Bank: {{ payment.bank_name }} <br>
                                                    Chq Date: {{ payment.cheque_date }}
                                                    <span v-if="payment.cheque_picture">
                                                        <br><a :href="payment.cheque_picture" target="_blank">View Cheque</a>
                                                    </span>
                                                </div>
                                                <div v-if="payment.description" class="text-muted mt-1 fst-italic">
                                                    {{ payment.description }}
                                                </div>
                                                <div v-if="payment.invoice_picture">
                                                    <a :href="payment.invoice_picture" target="_blank">View Invoice/Receipt</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <PaymentForm 
            v-if="showPaymentModal"
            :show="showPaymentModal"
            :company-balance="balance"
            @close="showPaymentModal = false"
        />
    </div>
</template>
