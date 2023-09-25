<template>
    <div ref="element" class="markdown p-2" v-html="text ? parseMarkdown(text) : ''"/>
</template>

<script setup lang="ts">
import { parseMarkdown } from "~~/utils/md-parser";

defineProps<{
    text?: string
}>();

const element = ref();

defineExpose({
    element
});
</script>