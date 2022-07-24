<template>
    <flex column :class="{banner: true, round: true, 'default-banner': !src}" :style="{height: `${height}px`, backgroundImage: `url('${bannerUrl}')`}">
        <slot/>
    </flex>
</template>
<script setup lang="ts">
const props = defineProps({
    src: String,
    urlPrefix: String,
    height: {
        type: Number,
        default: 300,
    }
});

const bannerUrl = computed(() => {
    const src = props.src;
    if (src) {
        if (src.startsWith("http://") || src.startsWith("https://") || src.startsWith("data:")) {
            return src;
        } else {
            return `http://localhost:8000/storage/${props.urlPrefix}/${src}`;
        }
    } else {
        return 'http://localhost:8000/storage/assets/default_banner.webp';
    }
});
</script>
<style>
.default-banner {
    background-repeat: repeat;
    background-size: auto;
}
</style>