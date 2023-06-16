<template>
    <img :src="compSrc" :alt="alt" :onerror="`this.src = '${props.fallback}'`">
</template>

<script setup lang="ts">
const props = defineProps({
    src: {
        default: '',
        type: [String, Blob],
    },
    urlPrefix: {
        type: String,
        default: ''
    },
    isAsset: {
        type: Boolean,
        default: false
    },
    fallback: String,
    alt: String
});

const compSrc = computed(() => useSrc(props.urlPrefix, props.src, props.isAsset) ?? props.fallback);
</script>