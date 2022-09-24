<template>
    <picture>
        <source v-if="compSrc" :srcset="compSrc">
        <source :srcset="noPreviewSrc" type="image/png">
        <img :src="noPreviewSrc" class="ratio-image round" alt="thumbnail" v-bind="$attrs">
    </picture>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';

const { public: config } = useRuntimeConfig();
const store = useStore();

const props = defineProps<{
    src?: string,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();

const noPreviewSrc = computed(() => `${config.apiUrl}/storage/assets/${store.theme === 'light' ? 'no-preview-light' : 'no-preview-dark'}.png`);

const compSrc = computed(() => {
    if (!props.src || isSrcExternal(props.src)) {
        return props.src;
    } else {
        return `${config.apiUrl}/storage/${props.urlPrefix}/${(props.hasThumb && !props.preferHq) ? 'thumb_' : ''}${props.src}`;
    }
});
</script>