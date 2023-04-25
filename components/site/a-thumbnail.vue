<template>
    <picture>
        <source v-if="compSrc" :srcset="compSrc">
        <source :srcset="noPreviewSrc" type="image/png">
        <img :src="noPreviewSrc" class="ratio-image round" alt="thumbnail" v-bind="$attrs">
    </picture>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
const store = useStore();

const props = defineProps<{
    src?: string|Blob,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();

const noPreviewSrc = computed(() => `/assets/${store.theme === 'light' ? 'no-preview-light' : 'no-preview-dark'}.png`);
const compSrc = computed(() => useSrc(props.urlPrefix, props.src, false, props.hasThumb && !props.preferHq));
</script>