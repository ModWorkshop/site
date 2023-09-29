<template>
    <a-img 
        loading="lazy"
        class="ratio-image round"
        :src="src"
        :url-prefix="urlPrefix"
        :fallback="noPreviewSrc"
        :use-thumb="hasThumb && !preferHq"
        :alt="$t('thumbnail')"
    />
</template>

<script setup lang="ts">
import { useStore } from '../../store';

const store = useStore();

defineProps<{
    src?: string|Blob,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();

const noPreviewSrc = computed(() => `/assets/${store.theme === 'light' ? 'no-preview-light' : 'no-preview-dark'}.png`);
</script>