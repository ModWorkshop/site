<template>
    <flex v-if="pageNumbers.length > 1">
        <template v-for="[i, n] in pageNumbers.entries()" :key="n">
            <a-button :disabled="modelValue == n" @click="setPage(n)">{{n}}</a-button>
            <a-button v-if="i == 0 && pageNumbers[1] > 2 || i == 5 && pageNumbers[6] - n > 1" disabled>...</a-button>
        </template>
        {{pages}}
        <slot/>
    </flex>
</template>
<script setup lang="ts">
import { clamp } from '@vueuse/shared';

const props = defineProps({
    modelValue: [Number, String],
    total: [Number, String],
    perPage: [Number, String],
    setQuery: [Boolean, String]
});

const emit = defineEmits([
    'update:modelValue',
    'update',
    'update:pages'
]);

const pages = computed(() => Math.ceil((props.total as number) / (props.perPage as number)));
watch(pages, val => emit('update:pages', val), { immediate: true });
const pageNumbers = computed(() => {
    if (!pages.value) {
        return [];
    }

    const page = clamp(parseInt(props.modelValue as string), 1, pages.value);
    const numbers = [page];

    const pagesToShow = Math.min(7, pages.value);
    const eachSide = (pagesToShow - 1)/2;
    const eachSideFollowing = pagesToShow/eachSide;

    let diffFromCenter = page < eachSide || pages.value < pagesToShow ? page - eachSide : 0;

    const diffFromEnd = pages.value - page;
    if (diffFromEnd < eachSide) {
        diffFromCenter = eachSide - diffFromEnd;
    }

    //Back
    let firstAdded = page === 1;
    for (let i = 1; i <= eachSideFollowing + diffFromCenter; i++) {
        if (page-1 === 1) {
            firstAdded = true;
        }
        numbers.unshift(page-i);
    }

    if (!firstAdded) {
        numbers.unshift(1);
    }

    //Front

    let lastAdded = page === pages.value;
    for (let i = 1; i <= eachSideFollowing - diffFromCenter; i++) {
        if (page+1 === 1) {
            lastAdded = true;
        }

        numbers.push(page+i);
    }

    if (!lastAdded) {
        numbers.push(pages.value);
    }


    return numbers;
});

function setPage(newPage) {
    emit('update:modelValue', newPage);
    emit('update', newPage);
}
</script>