<script setup>
import { computed, watch } from 'vue';
import SelectInput from './SelectInput.vue';
import FormError from './FormError.vue';

const props = defineProps({
    countries: {
        type: Array,
        default: () => [],
    },
    countryId: {
        type: [String, Number],
        default: '',
    },
    provinceId: {
        type: [String, Number],
        default: '',
    },
    cityId: {
        type: [String, Number],
        default: '',
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:countryId', 'update:provinceId', 'update:cityId']);

const countryOptions = computed(() => props.countries.map((country) => ({
    value: country.id,
    label: country.name,
})));

const selectedCountry = computed(() => props.countries.find((country) => String(country.id) === String(props.countryId)));

const provinceOptions = computed(() => (selectedCountry.value?.provinces ?? []).map((province) => ({
    value: province.id,
    label: province.name,
})));

const selectedProvince = computed(() => (selectedCountry.value?.provinces ?? []).find(
    (province) => String(province.id) === String(props.provinceId),
));

const cityOptions = computed(() => (selectedProvince.value?.cities ?? []).map((city) => ({
    value: city.id,
    label: city.name,
})));

watch(() => props.countryId, () => {
    if (! selectedCountry.value) {
        emit('update:provinceId', '');
        emit('update:cityId', '');
    }
});

watch(() => props.provinceId, () => {
    if (! selectedProvince.value) {
        emit('update:cityId', '');
    }
});
</script>

<template>
    <div class="col-3 mb-2">
        <SelectInput
            id="country_id"
            :model-value="countryId"
            label="Select Country *"
            placeholder="Select country"
            :options="countryOptions"
            @update:model-value="$emit('update:countryId', $event)"
        >
            <FormError :message="errors.country_id" />
        </SelectInput>
    </div>
    <div class="col-3 mb-2">
        <SelectInput
            id="province_id"
            :model-value="provinceId"
            label="Select Province *"
            placeholder="Select province"
            :options="provinceOptions"
            @update:model-value="$emit('update:provinceId', $event)"
        >
            <FormError :message="errors.province_id" />
        </SelectInput>
    </div>
    <div class="col-3 mb-2">
        <SelectInput
            id="city_id"
            :model-value="cityId"
            label="Select City *"
            placeholder="Select city"
            :options="cityOptions"
            @update:model-value="$emit('update:cityId', $event)"
        >
            <FormError :message="errors.city_id" />
        </SelectInput>
    </div>
</template>
