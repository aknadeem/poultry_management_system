<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    store: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Store Detail" :crumbs="['Home', 'ProductManagement', 'Product Stores', 'Show']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Store Detail Data</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('products.update')" :href="route('inertia.product-stores.edit', store)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 mb-2"><label class="fw-bold">Store Name</label><p class="mb-0">{{ store.store_name }}</p></div>
                    <div class="col-sm-6 mb-2"><label class="fw-bold">Store Code</label><p class="mb-0">{{ store.store_code }}</p></div>
                    <div class="col-sm-6 mb-2"><label class="fw-bold">Store Type</label><p class="mb-0">{{ store.store_type }}</p></div>
                    <div class="col-sm-6 mb-2"><label class="fw-bold">Total Racks</label><p class="mb-0">{{ store.total_racks }}</p></div>
                    <div class="col-sm-6 mb-2"><label class="fw-bold">Store Area (sqft)</label><p class="mb-0">{{ store.store_area }}</p></div>
                    <div class="col-sm-6 mb-2"><label class="fw-bold">Description</label><p class="mb-0">{{ store.description || '—' }}</p></div>
                </div>
            </div>
        </div>
    </div>
</template>
