<template>
    <flex column :class="{banner: true, round: true, 'default-banner': !src}" :style="{height: `${height}px`, backgroundImage: `url('${bannerUrl}')`}">
        <slot/>
    </flex>
</template>
<script setup lang="ts">
import { useStore } from '~~/store';
import { isSrcExternal } from '~~/utils/helpers';

const { public: config } = useRuntimeConfig();
const store = useStore();

const props = defineProps({
    src: [String, Blob],
    urlPrefix: String,
    height: {
        type: [Number, String],
        default: 300,
    }
});

const bannerUrl = computed(() => {
    const src = props.src;
    if (src) {
        if (typeof src === 'object') {
            return src.toString();
        } else if (isSrcExternal(src)) {
            return src;
        } else {
            return `${config.storageUrl}/${props.urlPrefix}/${src}`;
        }
    } else {
        return `${config.apiUrl}/assets/${store.theme == 'dark' ? 'default_banner' : 'dark_default_banner'}.webp`;
    }
});
</script>
<style>
.default-banner {
    background-repeat: repeat;
    background-size: auto;
}

.light .default-banner {
    background-color: #d6d8db;
}
</style>