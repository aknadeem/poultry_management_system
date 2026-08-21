<script setup>
import PageTitle from '../../Layouts/Partials/PageTitle.vue';
import FeedForm from '../../Components/Inventory/FeedForm.vue';
import { useRoute } from '../../Utils/route';

defineProps({
    categories: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    today: { type: String, default: '' },
});

const route = useRoute();
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Feeds" :crumbs="['Home', 'FeedManagement', 'Create']" />
        <div class="card">
            <div class="card-body">
                <FeedForm
                    form-title="Add Feed Entry"
                    :submit-url="route('inertia.feeds.store')"
                    :index-url="route('inertia.feeds.index')"
                    :categories="categories"
                    :companies="companies"
                    :today="today"
                />
            </div>
        </div>
    </div>
</template>
