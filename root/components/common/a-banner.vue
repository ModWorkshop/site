<template>
    <flex column :class="{banner: true, round: true, 'default-banner': !src}" :style="{height: `${height}px`, backgroundImage: `url('${bannerUrl}')`}">
        <slot/>
    </flex>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
const store = useStore();

const props = withDefaults(defineProps<{
    src: string|Blob,
    urlPrefix: string,
    height?: number|string,
}>(), {
    height: 300
});

const bannerUrl = computed(() => useSrc(props.urlPrefix, props.src) ?? `/assets/${store.theme == 'dark' ? 'default_banner' : 'dark_default_banner'}.webp`);
</script>