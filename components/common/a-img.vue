<template>
    <img :src="compSrc" :alt="alt" @error="useDefault = true">
</template>

<script setup lang="ts">
import { isSrcExternal } from '~~/utils/helpers';

const { public: config } = useRuntimeConfig();

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
const assetsUrl = `${config.apiUrl}/assets`;

const compSrc = computed(function() {
    if (useDefault.value) {
        return `${assetsUrl}/default-avatar.webp`;
    }
    const src = props.src;
    if (typeof src === 'object') {
        return src.toString();
    }
    else if (isSrcExternal(src)) {
        return src;
    } else {
        return `${props.isAsset ? assetsUrl : config.storageUrl}/${props.urlPrefix}/${src}`;
    }
});
</script>