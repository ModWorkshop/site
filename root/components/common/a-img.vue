<template>
    <img :loading="loading" :src="compSrc" :alt="alt" :onerror="`this.src = '${props.fallback}'`">
</template>

<script setup lang="ts">
const props = defineProps({
    src: {
        default: undefined,
        type: [String, Blob],
    },
    loading : {
        default: undefined,
        type: String
    },
    urlPrefix: {
        type: String,
        default: ''
    },
    isAsset: {
        type: Boolean,
        default: false
    },
    useThumb: {
        type: Boolean,
        default: false
    },
    fallback: String,
    alt: String
});

const compSrc = computed(() => (props.src ? useSrc(props.urlPrefix, props.src, props.isAsset, props.useThumb) : props.fallback) ?? props.fallback);
</script>