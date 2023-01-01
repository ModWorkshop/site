<template>
    <a-select :model-value="modelValue" :options="games.data" @update:model-value="val => $emit('update:modelValue', val)">
        <template #any-option="{ option }">
            <a-simple-game :game="option"/>
        </template>
    </a-select>
</template>

<script setup lang="ts">
import { Game } from '~~/types/models';

defineProps<{
    modelValue?: number;
}>();

defineEmits(['update:modelValue']);

const { data: games } = await useFetchMany<Game>('games', { initialCache: true });
</script>