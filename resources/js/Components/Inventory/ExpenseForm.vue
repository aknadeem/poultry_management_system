<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import FormError from '../Forms/FormError.vue';
import FormModal from '../Modal/FormModal.vue';
import PrimaryButton from '../Buttons/PrimaryButton.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    expense: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    today: { type: String, default: '' },
    formTitle: { type: String, default: 'Add Expense' },
    submitUrl: { type: String, required: true },
    indexUrl: { type: String, required: true },
    method: { type: String, default: 'post' },
});

const route = useRoute();
const page = usePage();
const categories = ref([...props.categories]);
const categoryOpen = ref(false);
const imagePreview = ref(props.expense?.picture_url ?? null);

const form = useForm({
    category_id: props.expense?.category_id ?? '',
    amount: props.expense?.amount ?? '',
    expense_date: props.expense?.expense_date ?? props.today,
    remarks: props.expense?.remarks ?? '',
    image_file: null,
});

const categoryForm = useForm({
    cat_name: '',
});

const sortedCategories = computed(() =>
    [...categories.value].sort((a, b) => String(a.name).localeCompare(String(b.name))),
);

function onFileChange(event) {
    const file = event.target.files[0] ?? null;
    form.image_file = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
}

function openCategoryModal() {
    categoryForm.reset();
    categoryForm.clearErrors();
    categoryOpen.value = true;
}

function submitCategory() {
    categoryForm.post(route('inertia.expenses.categories.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            const created = page.props.flash?.createdLookup;
            if (created?.id) {
                if (! categories.value.some((item) => Number(item.id) === Number(created.id))) {
                    categories.value = [...categories.value, { id: created.id, name: created.name }];
                }
                form.category_id = created.id;
            }
            categoryOpen.value = false;
        },
    });
}

function submit() {
    form.submit(props.method, props.submitUrl, { forceFormData: true });
}
</script>

<template>
    <div>
        <div class="row mb-2">
            <div class="col-6 align-self-start">
                <h4>{{ formTitle }}</h4>
            </div>
            <div class="col-6 align-self-end text-end mb-2">
                <Link :href="indexUrl" class="btn btn-secondary btn-sm" title="Click to go back">
                    <i class="fa fa-arrow-left"></i> Back
                </Link>
            </div>
        </div>
        <form autocomplete="off" enctype="multipart/form-data" @submit.prevent="submit">
            <div class="row form-group">
                <div class="col-6 mb-2 pe-0">
                    <label for="ExpenseCatSelect">Expense category *</label>
                    <div class="input-group">
                        <select id="ExpenseCatSelect" v-model="form.category_id" class="form-control">
                            <option value="">Select Category</option>
                            <option v-for="category in sortedCategories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <button
                            type="button"
                            class="btn input-group-text btn-dark btn-sm"
                            title="Click to add new category"
                            @click="openCategoryModal"
                        >
                            <i class="fa fa-plus pt-1"></i>
                        </button>
                    </div>
                    <FormError :message="form.errors.category_id" />
                </div>
                <div class="col-6 mb-2">
                    <label for="ExpenseAmount">Expense Amount *</label>
                    <input
                        id="ExpenseAmount"
                        v-model="form.amount"
                        class="form-control"
                        type="number"
                        step="any"
                        min="0"
                        placeholder="Enter Expense Amount"
                    >
                    <FormError :message="form.errors.amount" />
                </div>
                <div class="col-6 mb-2">
                    <label for="ExpenseDate">Date *</label>
                    <input id="ExpenseDate" v-model="form.expense_date" class="form-control" type="date">
                    <FormError :message="form.errors.expense_date" />
                </div>
                <div class="col-6 mb-2">
                    <label for="Remarks">Remarks *</label>
                    <textarea id="Remarks" v-model="form.remarks" class="form-control" rows="2" cols="80"></textarea>
                    <FormError :message="form.errors.remarks" />
                </div>
                <div class="col-sm-6 mt-2">
                    <label for="image">Image</label>
                    <input
                        id="image"
                        class="form-control"
                        type="file"
                        accept="image/jpeg,image/jpg,image/png"
                        @change="onFileChange"
                    >
                    <FormError :message="form.errors.image_file" />
                </div>
                <div v-if="imagePreview" class="col-sm-6 mt-2 img-holder d-flex align-items-center">
                    <img
                        :src="imagePreview"
                        alt="Expense image"
                        class="rounded"
                        style="max-height: 80px; border: 1px solid #ddd;"
                    >
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-4 mb-3">
                    <PrimaryButton type="submit" class="mt-3" :disabled="form.processing">Submit</PrimaryButton>
                    <Link :href="indexUrl" class="ms-1">
                        <SecondaryButton type="button" class="mt-3">Cancel</SecondaryButton>
                    </Link>
                </div>
            </div>
        </form>

        <FormModal :show="categoryOpen" title="Add Category" size="md" @close="categoryOpen = false">
            <form autocomplete="off" @submit.prevent="submitCategory">
                <div class="mb-2">
                    <label for="ExpenseCategory">Expense Category *</label>
                    <input
                        id="ExpenseCategory"
                        v-model="categoryForm.cat_name"
                        class="form-control"
                        type="text"
                        placeholder="Expense Category"
                        required
                    >
                    <FormError :message="categoryForm.errors.cat_name" />
                </div>
                <PrimaryButton type="submit" :disabled="categoryForm.processing">Submit</PrimaryButton>
                <SecondaryButton type="button" class="ms-1" @click="categoryOpen = false">Cancel</SecondaryButton>
            </form>
        </FormModal>
    </div>
</template>
