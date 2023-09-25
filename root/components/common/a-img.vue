<template>
    <img :loading="loading" :src="compSrc" :alt="alt" :onerror="`this.src = '${fallback}'`">
</template>

<script setup lang="ts">
const { 
    src,
    loading,
    urlPrefix = '',
    isAsset = false,
    useThumb = false,
    fallback
} = defineProps<{
    src?: string|Blob|null,
    loading?: string,
    urlPrefix?: string,
    isAsset?: boolean,
    useThumb?: boolean,
    fallback?: string,
    alt?: string
}>();

const compSrc = computed(() => (src ? useSrc(urlPrefix, src, isAsset, useThumb) : fallback) ?? fallback);
</script>