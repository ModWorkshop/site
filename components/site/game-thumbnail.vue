<template>
    <a-thumbnail :src="src" :has-thumb="false" url-prefix="games/thumbnails"/>
</template>

<script setup lang="ts">
defineProps<{
    src?: string|Blob
}>();
</script>