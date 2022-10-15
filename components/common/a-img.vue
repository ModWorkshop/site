<template>
    <img :src="compSrc" :width="width" :height="height" :alt="alt" @error="useDefault = true">
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
    width: [Number, String],
    height: [Number, String],
    alt: String
});

const useDefault = ref(false);

const compSrc = computed(function() {
    if (useDefault.value) {
        return `${config.apiUrl}/storage/assets/default-avatar.webp`;
    }
    const src = props.src;
    if (typeof src === 'object') {
        return src.toString();
    }
    else if (isSrcExternal(src)) {
        return src;
    } else {
        return `${config.apiUrl}/storage/${props.urlPrefix || ''}${src}`;
    }
});
</script>