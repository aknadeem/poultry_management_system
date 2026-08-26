<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import ProductStoreForm from '../../Components/ProductStores/ProductStoreForm.vue';
import { useRoute } from '../../Utils/route';

const props = defineProps({
    store: { type: Object, required: true },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="ProductManagement" :crumbs="['Home', 'ProductManagement', 'Product Stores', 'Edit']" />
        <div class="card">
            <div class="card-body">
                <ProductStoreForm
                    form-title="Edit Store Entry"
                    method="put"
                    :store="store"
                    :submit-url="route('inertia.product-stores.update', props.store)"
                    :index-url="route('inertia.product-stores.index')"
                />
            </div>
        </div>
    </div>
</template>
