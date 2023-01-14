<template>
    <a-select 
        v-model="vm"
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

const props = defineProps<{
    modelValue: number|User|Array<User>|undefine|null,
    placeholder?: string
}>();

const emit = defineEmits(['update:modelValue']);

const vm = useVModel(props, 'modelValue', emit);
</script>