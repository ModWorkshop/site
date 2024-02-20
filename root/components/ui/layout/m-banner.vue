<template>
    <m-flex column :class="{banner: true, round: true, 'default-banner': !src}" :style="{height: `${height}px`, backgroundImage: `url('${bannerUrl}')`}">
        <slot/>
    </m-flex>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
const store = useStore();

const props = withDefaults(defineProps<{
    src?: string|Blob,
    urlPrefix: string,
    height?: number|string,
}>(), {
    height: 300
});

const bannerUrl = computed(() => useSrc(props.urlPrefix, props.src) ?? `/assets/${store.theme == 'dark' ? 'default_banner' : 'dark_default_banner'}.webp`);
</script>

<style scope>
.banner {
    background-position: center;
    background-size: cover;
}

.default-banner {
    background-size: auto;
}
</style>