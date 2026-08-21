<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select...',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="form-group mb-2">
        <label :for="id">{{ label }}</label>
        <select
            :id="id"
            class="form-control"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">{{ placeholder }}</option>
            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
        <slot />
    </div>
</template>
