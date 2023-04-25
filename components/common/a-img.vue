<template>
    <img :src="compSrc" :alt="alt" @error="useDefault = true">
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
    alt: String
});

const useDefault = ref(false);
const assetsUrl = `/assets`;

const compSrc = computed(function() {
    if (useDefault.value) {
        return `${assetsUrl}/default-avatar.webp`;
    }
    return useSrc(props.urlPrefix, props.src, props.isAsset) ?? `${assetsUrl}/default-avatar.webp`;
});
</script>