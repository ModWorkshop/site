<template>
    <div class="markdown p-2" v-html="parseMarkdown(text)"/>
</template>

<script setup lang="ts">
import { parseMarkdown } from "~~/utils/md-parser";

defineProps<{
    text: string        
}>();
</script>