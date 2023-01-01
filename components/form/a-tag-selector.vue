<template>
    <a-select 
        :model-value="modelValue"
        :classic="false" 
        :clearable="false"
        @update:model-value="val => emit('update:modelValue', val)" 
        @fetched="options => emit('fetched', options)"
    />
</template>

<script setup lang="ts">
defineProps<{
    modelValue: any,
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string|number|any[]): void,
	(e: 'fetched', options: any[]): void
}>();
</script>