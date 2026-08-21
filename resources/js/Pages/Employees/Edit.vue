<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import EmployeeForm from '../../Components/Farms/EmployeeForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    employee: { type: Object, required: true },
    countries: { type: Array, default: () => [] },
    employeeTypes: { type: Array, default: () => [] },
    employeeLevels: { type: Array, default: () => [] },
    personalFarms: { type: Array, default: () => [] },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Edit Employee" :crumbs="['Home', 'Employee', 'Edit']" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <EmployeeForm
                            form-title="Edit"
                            method="put"
                            :employee="employee"
                            :submit-url="route('inertia.employees.update', props.employee)"
                            :index-url="route('inertia.employees.index')"
                            :countries="countries"
                            :employee-types="employeeTypes"
                            :employee-levels="employeeLevels"
                            :personal-farms="personalFarms"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
