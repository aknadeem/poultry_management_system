<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormModal from '../Modal/FormModal.vue';
import FormError from '../Forms/FormError.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    show: { type: Boolean, default: false },
    balance: { type: Object, required: true },
    today: { type: String, default: '' },
});

const emit = defineEmits(['close']);
const route = useRoute();

const form = useForm({
    balance_id: props.balance?.id,
    party_id: props.balance?.party_id,
    amount_payment: '',
    paid_date: props.today || '',
    payment_option: 'cheque',
    cheque_date: '',
    bank_name: '',
    reference_no: '',
    description: '',
    cheque_picture: null,
    image_file: null,
    idempotency_key: crypto.randomUUID(),
});

watch(() => props.balance, (balance) => {
    form.balance_id = balance?.id;
    form.party_id = balance?.party_id;
}, { immediate: true });

watch(() => props.show, (isOpen) => {
    if (! isOpen) {
        return;
    }

    form.reset();
    form.clearErrors();
    form.balance_id = props.balance?.id;
    form.party_id = props.balance?.party_id;
    form.paid_date = props.today || '';
    form.payment_option = 'cheque';
    form.idempotency_key = crypto.randomUUID();
});

const maxAmount = computed(() => Number(props.balance?.remaining_amount || 0));

function formatAmount(value) {
    return Number(value || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function onChequeFileChange(e) {
    form.cheque_picture = e.target.files[0] ?? null;
}

function onInvoiceFileChange(e) {
    form.image_file = e.target.files[0] ?? null;
}

function close() {
    form.reset();
    form.clearErrors();
    form.payment_option = 'cheque';
    form.paid_date = props.today || '';
    form.balance_id = props.balance?.id;
    form.party_id = props.balance?.party_id;
    form.idempotency_key = crypto.randomUUID();
    emit('close');
}

function submit() {
    form.post(route('inertia.party-balances.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <FormModal :show="show" title="Add Payment" size="lg" @close="close">
        <form autocomplete="off" class="form_loader" @submit.prevent="submit">
            <div class="row form-group">
                <div class="col-sm-4 mb-2">
                    <label>
                        Total Amount:
                        <span class="fw-bold fs-4">{{ formatAmount(balance?.total_amount) }}</span>
                    </label>
                </div>
                <div class="col-sm-4 mb-2">
                    <label>
                        Paid Amount:
                        <span class="fw-bold fs-4 text-success">{{ formatAmount(balance?.paid_amount) }}</span>
                    </label>
                </div>
                <div class="col-sm-4 mb-2">
                    <label>
                        Remaining Amount:
                        <span class="fw-bold fs-4 text-danger">{{ formatAmount(balance?.remaining_amount) }}</span>
                    </label>
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="amount_payment">Amount</label>
                    <input
                        id="amount_payment"
                        v-model="form.amount_payment"
                        class="form-control"
                        type="number"
                        step="any"
                        min="0"
                        :max="maxAmount"
                        placeholder="Enter amount"
                        required
                    >
                    <FormError :message="form.errors.amount_payment" />
                </div>

                <div class="col-sm-6 mb-2">
                    <label for="payment_option">Payment Option</label>
                    <select id="payment_option" v-model="form.payment_option" class="form-control" required>
                        <option value="cheque">Cheque</option>
                        <option value="cash">Cash</option>
                        <option value="other">Other</option>
                    </select>
                    <FormError :message="form.errors.payment_option" />
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="cheque_date">Cheque Date</label>
                    <input id="cheque_date" v-model="form.cheque_date" class="form-control" type="date" placeholder="Enter cheque date">
                    <FormError :message="form.errors.cheque_date" />
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="bank_name">Cheque Bank</label>
                    <input
                        id="bank_name"
                        v-model="form.bank_name"
                        class="form-control"
                        type="text"
                        placeholder="Enter bank name"
                    >
                    <FormError :message="form.errors.bank_name" />
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="cheque_picture">Cheque Picture</label>
                    <input
                        id="cheque_picture"
                        class="form-control"
                        type="file"
                        accept="image/jpeg,image/jpg,image/png"
                        @change="onChequeFileChange"
                    >
                    <FormError :message="form.errors.cheque_picture" />
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="reference_no">Reference Number</label>
                    <input
                        id="reference_no"
                        v-model="form.reference_no"
                        class="form-control"
                        type="text"
                        placeholder="Enter amount"
                    >
                    <FormError :message="form.errors.reference_no" />
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="paid_date">Payment Date *</label>
                    <input id="paid_date" v-model="form.paid_date" class="form-control" type="date" required>
                    <FormError :message="form.errors.paid_date" />
                </div>

                <div class="col-sm-4 mb-2">
                    <label for="image_file">Picture</label>
                    <input
                        id="image_file"
                        class="form-control"
                        type="file"
                        accept="image/jpeg,image/jpg,image/png"
                        @change="onInvoiceFileChange"
                    >
                    <FormError :message="form.errors.image_file" />
                </div>

                <div class="col-sm-12 mb-2">
                    <label for="description">Description</label>
                    <input
                        id="description"
                        v-model="form.description"
                        class="form-control"
                        type="text"
                        placeholder="Enter amount"
                    >
                    <FormError :message="form.errors.description" />
                </div>
            </div>

            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <button
                        type="submit"
                        class="btn btn-secondary btn-sm waves-effect waves-light mt-3"
                        :disabled="form.processing"
                    >
                        Submit
                    </button>
                    <button
                        type="button"
                        class="btn btn-light btn-sm waves-effect waves-light mt-3"
                        @click="close"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </FormModal>
</template>
