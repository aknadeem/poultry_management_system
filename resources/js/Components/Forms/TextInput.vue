<script setup>
defineProps({
    type: {
        type: String,
        default: 'text',
    },
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
    autocomplete: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="form-group mb-2">
        <label :for="id">{{ label }}</label>
        <input
            :id="id"
            class="form-control"
            :type="type"
            :value="modelValue"
            :autocomplete="autocomplete"
            :placeholder="placeholder"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <slot />
    </div>
</template>
