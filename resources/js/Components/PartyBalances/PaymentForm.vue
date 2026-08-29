<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../Modal/Modal.vue';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
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
    payment_option: 'cash',
    cheque_date: '',
    bank_name: '',
    description: '',
    cheque_picture: null,
    image_file: null,
});

watch(() => props.balance, (balance) => {
    form.balance_id = balance?.id;
    form.party_id = balance?.party_id;
}, { immediate: true });

const maxAmount = computed(() => Number(props.balance?.remaining_amount || 0));
const isCheque = computed(() => form.payment_option === 'cheque');

function onChequeFileChange(e) {
    form.cheque_picture = e.target.files[0] ?? null;
}

function onInvoiceFileChange(e) {
    form.image_file = e.target.files[0] ?? null;
}

function close() {
    form.reset();
    form.clearErrors();
    form.payment_option = 'cash';
    form.paid_date = props.today || '';
    form.balance_id = props.balance?.id;
    form.party_id = props.balance?.party_id;
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
    <Modal :show="show" max-width="lg" @close="close">
        <div class="modal-header">
            <h5 class="modal-title">Record Payment for {{ balance?.party_name }}</h5>
            <button type="button" class="btn-close" @click="close"></button>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Party Name</label>
                        <input class="form-control" type="text" :value="balance?.party_name" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Payment Status</label>
                        <input class="form-control" type="text" :value="balance?.payment_status" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Total Amount</label>
                        <input class="form-control" type="text" :value="balance?.total_amount" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Remaining Amount</label>
                        <input class="form-control" type="text" :value="balance?.remaining_amount" disabled>
                    </div>
                    <div class="col-md-12"><hr></div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Amount Payment *</label>
                        <input v-model="form.amount_payment" class="form-control" type="number" step="any" min="1" :max="maxAmount" placeholder="Enter amount">
                        <FormError :message="form.errors.amount_payment" />
                        <small class="text-muted">Maximum: {{ maxAmount }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Paid Date *</label>
                        <input v-model="form.paid_date" class="form-control" type="date">
                        <FormError :message="form.errors.paid_date" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Payment Option *</label>
                        <select v-model="form.payment_option" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                        <FormError :message="form.errors.payment_option" />
                    </div>

                    <template v-if="isCheque">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Cheque Date *</label>
                            <input v-model="form.cheque_date" class="form-control" type="date">
                            <FormError :message="form.errors.cheque_date" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Bank Name *</label>
                            <input v-model="form.bank_name" class="form-control" type="text" placeholder="Enter bank name">
                            <FormError :message="form.errors.bank_name" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Cheque Picture *</label>
                            <input class="form-control" type="file" accept="image/jpeg,image/jpg,image/png" @change="onChequeFileChange">
                            <FormError :message="form.errors.cheque_picture" />
                        </div>
                    </template>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Invoice/Receipt Picture</label>
                        <input class="form-control" type="file" accept="image/jpeg,image/jpg,image/png" @change="onInvoiceFileChange">
                        <FormError :message="form.errors.image_file" />
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Narration</label>
                        <textarea v-model="form.description" class="form-control" rows="2" placeholder="Description/Note"></textarea>
                        <FormError :message="form.errors.description" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <SecondaryButton type="button" @click="close">Cancel</SecondaryButton>
                <PrimaryButton type="submit" :disabled="form.processing">Submit Payment</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
