<template>
    <m-img 
        :loading="lazy ? 'lazy' : undefined"
        class="ratio-image round"
        :src="src"
        :url-prefix="urlPrefix"
        fallback="/assets/no-preview.webp"
        :use-thumb="hasThumb && !preferHq"
        :alt="$t('thumbnail')"
    />
</template>

<script setup lang="ts">
const { lazy = true } = defineProps<{
    src?: string|Blob,
    lazy?: bool,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();
</script>