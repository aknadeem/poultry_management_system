<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    expense: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Expense" :crumbs="['Home', 'ExpenseManagement', 'Detail']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Expense Detail</h4></div>
                    <div class="col-6 text-end">
                        <Link
                            v-if="can('expenses.update')"
                            :href="route('inertia.expenses.edit', expense)"
                            class="btn btn-info btn-sm me-1"
                        >
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                        <Link :href="route('inertia.expenses.index')" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </Link>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Expense Code</th>
                                    <td>{{ expense.expense_code || '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ expense.category_name || '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ expense.expense_date_label || expense.expense_date || '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td><b>{{ expense.amount }}</b></td>
                                </tr>
                                <tr>
                                    <th>Remarks</th>
                                    <td>{{ expense.remarks || '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 text-center">
                        <img
                            v-if="expense.picture_url"
                            :src="expense.picture_url"
                            alt="Expense"
                            class="img-fluid rounded"
                            style="max-height: 220px; border: 1px solid #ddd;"
                        >
                        <div v-else class="text-muted">No Image</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
