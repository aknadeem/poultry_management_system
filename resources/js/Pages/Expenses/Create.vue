<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ExpenseForm from '../../Components/Inventory/ExpenseForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    categories: { type: Array, default: () => [] },
    today: { type: String, default: '' },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Expense" :crumbs="['Home', 'ExpenseManagement', 'Create']" />
        <div class="card">
            <div class="card-body">
                <ExpenseForm
                    form-title="Add Expense"
                    :submit-url="route('inertia.expenses.store')"
                    :index-url="route('inertia.expenses.index')"
                    :categories="categories"
                    :today="today"
                />
            </div>
        </div>
    </div>
</template>
