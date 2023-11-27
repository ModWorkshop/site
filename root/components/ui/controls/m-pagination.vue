<template>
    <m-flex v-if="noHiding || pageNumbers.length > 1">
        <template v-for="[i, n] in pageNumbers.entries()" :key="n">
            <m-button :disabled="actualPage == n" @click="setPage(n)">{{n}}</m-button>
            <m-button v-if="i == 0 && pageNumbers[1] > 2 || i == defaultMaxNumbers-2 && pages - n > 1" disabled>...</m-button>
        </template>
        <slot/>
    </m-flex>
</template>
<script setup lang="ts">
import { clamp } from '@vueuse/shared';

const props = defineProps({
    modelValue: [Number, String],
    total: [Number, String],
    perPage: [Number, String],
    setQuery: [Boolean, String],
    noHiding: [Boolean, String]
});

const emit = defineEmits([
    'update:modelValue',
    'update',
    'update:pages'
]);

const defaultMaxNumbers = 7; //Should be an odd number so the current page shows in the middle, minimum of 3.

const pages = computed(() => Math.ceil((props.total as number) / (props.perPage as number)));

//Make sure whatever page we got is valid
const actualPage = computed(() => clamp(parseInt(props.modelValue as string), 1, pages.value));

watch(pages, val => emit('update:pages', val), { immediate: true });
const pageNumbers = computed(() => {
    if (pages.value <= 1 && !props.noHiding) {
        return [];
    }

    //How many more numbers we should insert to handle cases we have less pages than max
    const maxNumbers = Math.min(defaultMaxNumbers - 2, pages.value - 2);
    const numbersEachSide = (defaultMaxNumbers - 3) / 2; //Excluding the edges
    
    const startingPoint = clamp(actualPage.value-numbersEachSide, 2, pages.value - maxNumbers);

    const numbers = [1];

    for (let i = 0; i < maxNumbers; i++) {
        numbers.push(startingPoint+i);
    }

    if (pages.value > 1) {
        numbers.push(pages.value);
    }

    return numbers;
});

function setPage(newPage) {
    emit('update:modelValue', newPage);
    emit('update', newPage);
}
</script>