<template>
    <div class="markdown" v-html="parseMarkdown(text)"/>
</template>

<script setup lang="ts">
import { parseMarkdown } from "~~/utils/md-parser";
defineProps({
    text: String        
});
</script>