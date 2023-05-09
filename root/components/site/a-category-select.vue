<template>
    <a-input :label="$t('category')">
        <category-tree 
            :model-value="modelValue"
            v-bind="$attrs"
            :style="{maxHeight: maxHeight && `${maxHeight}px`}"
            :class="{'input-bg': true, 'p-2': true, 'overflow-y-scroll': !!maxHeight}"
            :categories="categories"
            @update:model-value="val => $emit('update:modelValue', val)"
        />
    </a-input>
</template>

<script setup lang="ts">
import { Category } from '~~/types/models';

withDefaults(defineProps<{
    modelValue?: number;
    categories: Category[];
    maxHeight?: number;
}>(), { maxHeight: 350 });

defineEmits(['update:modelValue']);
</script>