<template>
    <a-select 
        v-model="modelValue"
        url="users"
        :placeholder="placeholder"
        @update:model-value="value => $emit('update:modelValue', value)">
        <template #list-option="{ option }">
            <a-user :user="option" avatar-size="xs" static show-at/>
        </template>
        <template #option="{ option }">
            <a-user :user="option" avatar-size="xs" static show-at/>
        </template>
    </a-select>
</template>

<script setup lang="ts">
import { User } from '~~/types/models';

withDefaults(defineProps<{
    modelValue: number|User|Array<User>,
    placeholder?: string
}>(), { placeholder: 'Select a user...' });

defineEmits(['update:modelValue']);
</script>