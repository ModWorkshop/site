<template>
    <flex>
        <template v-if="pages > 5">
            <a-button v-if="modelValue > 3" @click="setPage(1)">1</a-button>
            <a-button disabled v-if="pageNumbers[0] > 2">...</a-button>
        </template>
        <a-button :disabled="modelValue == n" @click="setPage(n)" v-for="n in pageNumbers">
            {{n}}
        </a-button>
        <template v-if="pages > 5">
            <a-button v-if="pages - pageNumbers[pageNumbers.length-1] > 1" disabled>...</a-button>
            <a-button v-if="pages - modelValue > 2" @click="setPage(pages)" :disabled="modelValue == pages">{{pages}}</a-button>
        </template>
    </flex>
</template>
<script setup>
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

const pages = computed(() => parseInt(props.total / props.perPage || 0));
watch(pages, val => emit('update:pages', val), { immediate: true });
const pageNumbers = computed(() => {
    if (props.modelValue < 4) {
        return [...Array(Math.min(5, pages.value)).keys()].map(x => x + 1);
    } else if (pages.value - page.value > 2) {
        return [...Array(5).keys()].map(x => x + props.modelValue-2);
    } else {
        return [...Array(5).keys()].map(x => pages.value - 4 + x);
    }
});

function setPage(newPage) {
    props.modelValue = newPage;
    emit('update:modelValue', newPage);
    emit('update', newPage);
}
</script>