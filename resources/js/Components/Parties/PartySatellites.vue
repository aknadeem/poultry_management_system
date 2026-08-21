<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import FormModal from '../Modal/FormModal.vue';
import FormError from '../Forms/FormError.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';
import { usePermissions } from '../../Composables/usePermissions';

const route = useRoute();
const { can } = usePermissions();

const addAccountOpen = ref(false);
const viewAccountsOpen = ref(false);
const addDocumentOpen = ref(false);
const viewDocumentsOpen = ref(false);
const addLimitOpen = ref(false);
const viewLimitsOpen = ref(false);
const currentParty = ref(null);

const accountForm = useForm({
    party_id: '',
    account_title: '',
    account_number: '',
    bank_name: '',
    opening_balance: '',
});

const documentForm = useForm({
    party_id: '',
    document_title: '',
    document_name: null,
});

const limitForm = useForm({
    party_id: '',
    start_date: '',
    end_date: '',
    debit_limit: '',
    credit_limit: '',
});

function openAddAccount(party) {
    currentParty.value = party;
    accountForm.reset();
    accountForm.clearErrors();
    accountForm.party_id = party.id;
    addAccountOpen.value = true;
}

function openViewAccounts(party) {
    currentParty.value = party;
    viewAccountsOpen.value = true;
}

function openAddDocument(party) {
    currentParty.value = party;
    documentForm.reset();
    documentForm.clearErrors();
    documentForm.party_id = party.id;
    documentForm.document_name = null;
    addDocumentOpen.value = true;
}

function openViewDocuments(party) {
    currentParty.value = party;
    viewDocumentsOpen.value = true;
}

function openAddLimit(party) {
    currentParty.value = party;
    limitForm.reset();
    limitForm.clearErrors();
    limitForm.party_id = party.id;
    addLimitOpen.value = true;
}

function openViewLimits(party) {
    currentParty.value = party;
    viewLimitsOpen.value = true;
}

function submitAccount() {
    accountForm.post(route('inertia.party-accounts.store'), {
        forceFormData: true,
        onSuccess: () => {
            addAccountOpen.value = false;
        },
    });
}

function submitDocument() {
    documentForm.post(route('inertia.party-documents.store'), {
        forceFormData: true,
        onSuccess: () => {
            addDocumentOpen.value = false;
        },
    });
}

function submitLimit() {
    limitForm.post(route('inertia.party-balance-limits.store'), {
        onSuccess: () => {
            addLimitOpen.value = false;
        },
    });
}

function destroyAccount(account) {
    router.delete(route('inertia.party-accounts.destroy', account), {
        preserveScroll: true,
    });
}

function destroyDocument(document) {
    router.delete(route('inertia.party-documents.destroy', document), {
        preserveScroll: true,
    });
}

function destroyLimit(limit) {
    router.delete(route('inertia.party-balance-limits.destroy', limit), {
        preserveScroll: true,
    });
}

function onDocumentFile(event) {
    documentForm.document_name = event.target.files[0] ?? null;
}

defineExpose({
    openAddAccount,
    openViewAccounts,
    openAddDocument,
    openViewDocuments,
    openAddLimit,
    openViewLimits,
});
</script>

<template>
    <FormModal :show="addAccountOpen" title="Add Bank account" @close="addAccountOpen = false">
        <form autocomplete="off" @submit.prevent="submitAccount">
            <div class="row form-group">
                <div class="col-sm-6 mb-2">
                    <label for="pAccountTitle">Account Title *</label>
                    <input id="pAccountTitle" v-model="accountForm.account_title" class="form-control" type="text" placeholder="Enter Account Title">
                    <FormError :message="accountForm.errors.account_title" />
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="pAccountNo">Account Number *</label>
                    <input id="pAccountNo" v-model="accountForm.account_number" class="form-control" type="text" placeholder="Enter Account number">
                    <FormError :message="accountForm.errors.account_number" />
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="pAccountBankName">Bank Name *</label>
                    <input id="pAccountBankName" v-model="accountForm.bank_name" class="form-control" type="text" placeholder="Enter Bank name">
                    <FormError :message="accountForm.errors.bank_name" />
                </div>
                <div class="col-6 mb-2">
                    <label for="pOpeningBalance">Opening Balance *</label>
                    <input id="pOpeningBalance" v-model="accountForm.opening_balance" class="form-control" type="number" min="0" step="any" placeholder="Enter opening balance">
                    <FormError :message="accountForm.errors.opening_balance" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="accountForm.processing">Submit</PrimaryButton>
                    <SecondaryButton type="button" class="ms-1" @click="addAccountOpen = false">Cancel</SecondaryButton>
                </div>
            </div>
        </form>
    </FormModal>

    <FormModal :show="viewAccountsOpen" title="Account Detail" @close="viewAccountsOpen = false">
        <div class="table-responsive">
            <table class="table table-striped w-100 mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Account Number</th>
                        <th>Bank</th>
                        <th>Opening Balance</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="! currentParty?.accounts?.length">
                        <td colspan="5" class="text-center text-muted">No accounts found</td>
                    </tr>
                    <tr v-for="account in currentParty?.accounts ?? []" :key="account.id">
                        <td>{{ account.account_title }}</td>
                        <td>{{ account.account_number }}</td>
                        <td>{{ account.bank_name }}</td>
                        <td>{{ account.opening_balance }}</td>
                        <td class="text-end">
                            <a
                                v-if="can('parties.delete')"
                                href="javascript:void(0);"
                                class="btn btn-danger btn-sm"
                                @click="destroyAccount(account)"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </FormModal>

    <FormModal :show="addDocumentOpen" title="Add Documents" @close="addDocumentOpen = false">
        <form autocomplete="off" @submit.prevent="submitDocument">
            <div class="row form-group">
                <div class="col-sm-6 mb-2">
                    <label for="pDocumentTitle">Title *</label>
                    <input id="pDocumentTitle" v-model="documentForm.document_title" class="form-control" type="text" placeholder="Enter Document title">
                    <FormError :message="documentForm.errors.document_title" />
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="pDocuments">document *</label>
                    <input id="pDocuments" class="form-control" type="file" @change="onDocumentFile">
                    <FormError :message="documentForm.errors.document_name" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="documentForm.processing">Submit</PrimaryButton>
                    <SecondaryButton type="button" class="ms-1" @click="addDocumentOpen = false">Cancel</SecondaryButton>
                </div>
            </div>
        </form>
    </FormModal>

    <FormModal :show="viewDocumentsOpen" title="Documents" @close="viewDocumentsOpen = false">
        <div class="table-responsive">
            <table class="table table-striped w-100 mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="! currentParty?.documents?.length">
                        <td colspan="3" class="text-center text-muted">No documents found</td>
                    </tr>
                    <tr v-for="document in currentParty?.documents ?? []" :key="document.id">
                        <td>{{ document.title }}</td>
                        <td>
                            <a v-if="document.url" :href="document.url" target="_blank">View file</a>
                            <span v-else>—</span>
                        </td>
                        <td class="text-end">
                            <a
                                v-if="can('parties.delete')"
                                href="javascript:void(0);"
                                class="btn btn-danger btn-sm"
                                @click="destroyDocument(document)"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </FormModal>

    <FormModal :show="addLimitOpen" title="Add Debit/Credit Limit" @close="addLimitOpen = false">
        <form autocomplete="off" @submit.prevent="submitLimit">
            <div class="row form-group">
                <div class="col-sm-6 mb-2">
                    <label for="pStartDate">Start Date *</label>
                    <input id="pStartDate" v-model="limitForm.start_date" class="form-control" type="date">
                    <FormError :message="limitForm.errors.start_date" />
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="pEndDate">End Date *</label>
                    <input id="pEndDate" v-model="limitForm.end_date" class="form-control" type="date">
                    <FormError :message="limitForm.errors.end_date" />
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="pDebitLimit">Debit Limit*</label>
                    <input id="pDebitLimit" v-model="limitForm.debit_limit" class="form-control" type="number" min="0" step="any" placeholder="Enter Debit Limit">
                    <FormError :message="limitForm.errors.debit_limit" />
                </div>
                <div class="col-sm-6 mb-2">
                    <label for="pCreditLimit">Credit Limit *</label>
                    <input id="pCreditLimit" v-model="limitForm.credit_limit" class="form-control" type="number" min="0" step="any" placeholder="Enter credit Limit">
                    <FormError :message="limitForm.errors.credit_limit" />
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" :disabled="limitForm.processing">Submit</PrimaryButton>
                    <SecondaryButton type="button" class="ms-1" @click="addLimitOpen = false">Cancel</SecondaryButton>
                </div>
            </div>
        </form>
    </FormModal>

    <FormModal :show="viewLimitsOpen" title="Debit/Credit Limit" @close="viewLimitsOpen = false">
        <div class="table-responsive">
            <table class="table table-striped w-100 mb-0">
                <thead>
                    <tr>
                        <th>Start</th>
                        <th>End</th>
                        <th>Debit Limit</th>
                        <th>Credit Limit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="! currentParty?.balance_limits?.length">
                        <td colspan="5" class="text-center text-muted">No limits found</td>
                    </tr>
                    <tr v-for="limit in currentParty?.balance_limits ?? []" :key="limit.id">
                        <td>{{ limit.start_date }}</td>
                        <td>{{ limit.end_date }}</td>
                        <td>{{ limit.debit_limit }}</td>
                        <td>{{ limit.credit_limit }}</td>
                        <td class="text-end">
                            <a
                                v-if="can('parties.delete')"
                                href="javascript:void(0);"
                                class="btn btn-danger btn-sm"
                                @click="destroyLimit(limit)"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </FormModal>
</template>
