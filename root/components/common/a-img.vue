<template>
    <img :loading="loading" :src="compSrc" :alt="alt" @error="onError">
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

const forceSrc = ref();
const compSrc = computed(() => forceSrc.value ?? (src ? useSrc(urlPrefix, src, isAsset, useThumb) : fallback) ?? fallback);
const errorFired = ref(false);

function onError() {
    if (errorFired.value) {
        return;
    }
    errorFired.value = true;
    forceSrc.value = fallback;
}
</script>