<script setup>
import { Link } from '@inertiajs/vue3';
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

defineProps({
    product: { type: Object, required: true },
});

const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Product Detail" :crumbs="['Home', 'ProductManagement', 'Products', 'Show']" />
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><h4>Product Detail Data</h4></div>
                    <div class="col-6 text-end">
                        <Link v-if="can('products.update')" :href="route('inertia.products.edit', product)" class="btn btn-info btn-sm">
                            <i class="fa fa-pencil-alt"></i> Edit
                        </Link>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 mb-2"><label>Name</label><p class="mb-0">{{ product.product_name }}</p></div>
                    <div class="col-sm-6 mb-2"><label>Code</label><p class="mb-0">{{ product.product_code }}</p></div>
                    <div class="col-sm-6 mb-2"><label>Type</label><p class="mb-0">{{ product.product_type_name || '—' }}</p></div>
                    <div class="col-sm-6 mb-2"><label>Company</label><p class="mb-0">{{ product.company_name || '—' }}</p></div>
                    <div class="col-sm-6 mb-2"><label>Category</label><p class="mb-0">{{ product.category_name || '—' }}</p></div>
                    <div class="col-sm-6 mb-2"><label>Quantity</label><p class="mb-0">{{ product.quantity }}</p></div>
                    <div v-if="product.product_picture_url" class="col-sm-6 mb-2">
                        <label>Picture</label>
                        <p class="mb-0"><img :src="product.product_picture_url" alt="Product" class="avatar-lg"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
