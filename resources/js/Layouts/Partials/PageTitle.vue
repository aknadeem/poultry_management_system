<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    crumbs: {
        type: Array,
        default: () => ['Home'],
    },
});
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li
                            v-for="(crumb, index) in crumbs"
                            :key="`${crumb}-${index}`"
                            class="breadcrumb-item"
                            :class="{ active: index === crumbs.length - 1 }"
                        >
                            <a v-if="index !== crumbs.length - 1" href="#">{{ crumb }}</a>
                            <template v-else>{{ crumb }}</template>
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">{{ title }}</h4>
            </div>
        </div>
    </div>
</template>
