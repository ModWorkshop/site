<template>
    <div class="markdown" v-html="parseMarkdown(text)"/>
</template>

<script setup>
    import { parseMarkdown } from "../../utils/md-parser";
    defineProps({
        text: String        
    });
</script>