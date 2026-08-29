<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ExpenseForm from '../../Components/Inventory/ExpenseForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    expense: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    today: { type: String, default: '' },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Expense" :crumbs="['Home', 'ExpenseManagement', 'Edit']" />
        <div class="card">
            <div class="card-body">
                <ExpenseForm
                    form-title="Update Expense"
                    :expense="expense"
                    :submit-url="route('inertia.expenses.update', expense)"
                    :index-url="route('inertia.expenses.index')"
                    :categories="categories"
                    :today="today"
                    method="put"
                />
            </div>
        </div>
    </div>
</template>
