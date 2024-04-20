<template>
    <m-img 
        loading="lazy"
        class="ratio-image round"
        :src="src"
        :url-prefix="urlPrefix"
        fallback="/assets/no-preview.webp"
        :use-thumb="hasThumb && !preferHq"
        :alt="$t('thumbnail')"
    />
</template>

<script setup lang="ts">
defineProps<{
    src?: string|Blob,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();
</script>