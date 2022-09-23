<template>
    <flex column :class="{banner: true, round: true, 'default-banner': !src}" :style="{height: `${height}px`, backgroundImage: `url('${bannerUrl}')`}">
        <slot/>
    </flex>
</template>
<script setup lang="ts">
const { public: config } = useRuntimeConfig();

const props = defineProps({
    src: String,
    urlPrefix: String,
    height: {
        type: [Number, String],
        default: 300,
    }
});

const bannerUrl = computed(() => {
    const src = props.src;
    if (src) {
        if (isSrcExternal(src)) {
            return src;
        } else {
            return `${config.apiUrl}/storage/${props.urlPrefix}/${src}`;
        }
    } else {
        return `${config.apiUrl}/storage/assets/default_banner.webp`;
    }
});
</script>
<style>
.default-banner {
    background-repeat: repeat;
    background-size: auto;
}

.light .default-banner {
    background-color: #000;
}
</style>