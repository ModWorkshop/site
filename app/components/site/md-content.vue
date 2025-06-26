<!-- eslint-disable vue/no-v-html -->
<template>
    <div ref="element" :class="{'markdown': true, [`p-${padding}`]: padding > 0}" v-html="mdText"/>
</template>

<script setup lang="ts">
// You may wonder why the hell would you want this in a markdown content element?
// Simple, you may want to parse the markdown, but actually show no tags
// This is useful in places like announcements where you have to cut the content, making it very odd to display formatted.
// It does keep line breaks and paragraphs, though.

const { text, padding = 2, parserVersion, removeTags } = defineProps<{
    text?: string,
    padding?: number,
    parserVersion?: number,
    removeTags?: boolean
}>();

const element = ref();
defineExpose({
    element
});

const mdText = computed(() => {
    if (!text) {
        return '';
    }

    if (parserVersion == 1) {
        return oldParseMarkdown(text);
    } else {
        return parseMarkdown(text, removeTags);
    }
});
</script>