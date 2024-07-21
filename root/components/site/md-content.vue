<!-- eslint-disable vue/no-v-html -->
<template>
    <div ref="element" :class="{'markdown': true, [`p-${padding}`]: padding > 0}" v-html="text ? parseMarkdown(text) : ''"/>
</template>

<script setup lang="ts">
import { parseMarkdown } from "~~/utils/md-parser";

const { padding = 2 } = defineProps<{
    text?: string,
    padding?: number,
}>();

const element = ref();

defineExpose({
    element
});
</script>