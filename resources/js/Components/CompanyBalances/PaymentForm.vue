<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../Modal/Modal.vue';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    companyBalance: { type: Object, required: true },
});

const emit = defineEmits(['close']);

const form = useForm({
    company_balance_id: props.companyBalance?.id,
    company_id: props.companyBalance?.company_id,
    amount_payment: '',
    payment_option: 1, // 1 = Cash, 2 = Cheque
    cheque_date: '',
    bank_name: '',
    description: '',
    cheque_picture: null,
    image_file: null, // invoice picture
});

const maxAmount = computed(() => {
    return Number(props.companyBalance?.remaining_amount || 0);
});

const isCheque = computed(() => form.payment_option == 2);

function onChequeFileChange(e) {
    form.cheque_picture = e.target.files[0];
}

function onInvoiceFileChange(e) {
    form.image_file = e.target.files[0];
}

function close() {
    form.reset();
    form.clearErrors();
    emit('close');
}

function submit() {
    form.post(route('inertia.company-balances.store'), {
        forceFormData: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <Modal :show="show" max-width="lg" @close="close">
        <div class="modal-header">
            <h5 class="modal-title">Record Payment for {{ companyBalance?.company_name }}</h5>
            <button type="button" class="btn-close" @click="close"></button>
        </div>
        <form autocomplete="off" @submit.prevent="submit">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Company Name</label>
                        <input class="form-control" type="text" :value="companyBalance?.company_name" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Payment Status</label>
                        <input class="form-control" type="text" :value="companyBalance?.status" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Total Amount</label>
                        <input class="form-control" type="text" :value="companyBalance?.total_amount" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Remaining Amount</label>
                        <input class="form-control" type="text" :value="companyBalance?.remaining_amount" disabled>
                    </div>
                    <div class="col-md-12"><hr></div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Amount Payment *</label>
                        <input v-model="form.amount_payment" class="form-control" type="number" step="any" min="1" :max="maxAmount" placeholder="Enter amount">
                        <FormError :message="form.errors.amount_payment" />
                        <small class="text-muted">Maximum: {{ maxAmount }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Payment Method *</label>
                        <select v-model="form.payment_option" class="form-select">
                            <option value="1">Cash</option>
                            <option value="2">Cheque</option>
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
                            <label class="fw-bold">Cheque Picture</label>
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
