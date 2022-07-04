<template>
    <textarea v-if="type == 'textarea'" class="input" :maxlength="maxlength" :rows="rows" @input="$emit('update:modelValue', modelValue)">{{modelValue}}</textarea>
    <input v-else v-model="modelValue" @input="$emit('update:modelValue', modelValue)" class="input" :type="type" :maxlength="maxlength"/>
</template>

<script setup>
    defineProps({
        modelValue: String,
        maxlength: [Number, String],
        rows: [Number, String],
        type: String,
        value: String
    });

    defineEmits([
        'update:modelValue', 
    ]);
</script>

<style scoped>
    .input {
        padding: 0.6rem;
        width: 100%;
        color: var(--text-color);
        background-color: var(--input-bg-color);
        border: 1px solid var(--input-border-color);
        border-radius: var(--border-radius);
    }
</style>