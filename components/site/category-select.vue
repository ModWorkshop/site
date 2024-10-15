<template>
    <m-input :label="$t('category')">
        <category-tree 
            v-model="vm"
            v-bind="$attrs"
            :set-query="setQuery"
            :style="{maxHeight: maxHeight && `${maxHeight}px`}"
            :class="{'input-bg': true, 'p-2': true, 'overflow-y-scroll': !!maxHeight}"
            :categories="categories"
        />
    </m-input>
</template>

<script setup lang="ts">
import type { Category } from '~~/types/models';

const props = withDefaults(defineProps<{
    modelValue?: number|number[]|null,
    setQuery?: boolean,
    categories: Category[],
    maxHeight?: number,
}>(), { maxHeight: 350 });

const emit = defineEmits(['update:modelValue']);
const vm = useVModel(props, 'modelValue', emit);
</script>