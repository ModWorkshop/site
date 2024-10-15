<!-- eslint-disable vue/no-v-html -->
<template>
    <div ref="element" :class="{'markdown': true, [`p-${padding}`]: padding > 0}" v-html="mdText"/>
</template>

<script setup lang="ts">
import { parseMarkdown } from "~~/utils/md-parser";
import { oldParseMarkdown } from "~~/utils/old-md-parser";

const { text, padding = 2, oldParser } = defineProps<{
    text?: string,
    padding?: number,
    oldParser?: boolean
}>();

const element = ref();
defineExpose({
    element
});

const mdText = computed(() => {
    if (!text) {
        return '';
    }

    if (oldParser) {
        return oldParseMarkdown(text);
    } else {
        return parseMarkdown(text);
    }
});
</script>