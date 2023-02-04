<template>
    <picture>
        <source v-if="compSrc" :srcset="compSrc">
        <source :srcset="noPreviewSrc" type="image/png">
        <img :src="noPreviewSrc" class="ratio-image round" alt="thumbnail" v-bind="$attrs">
    </picture>
</template>

<script setup lang="ts">
import { useStore } from '~~/store';
import { isSrcExternal } from '~~/utils/helpers';

const { public: config } = useRuntimeConfig();
const store = useStore();

const props = defineProps<{
    src?: string|Blob,
    preferHq?: boolean,
    hasThumb?: boolean,
    urlPrefix: string,
}>();

const noPreviewSrc = computed(() => `${config.apiUrl}/storage/assets/${store.theme === 'light' ? 'no-preview-light' : 'no-preview-dark'}.png`);

const compSrc = computed(() => {
    const src = props.src;
    if (src) {
        if (typeof src == 'object') {
            return src.toString();
        }
        else if (!src || isSrcExternal(src)) {
            return src;
        } else if (config.debug_legacy_images) {
            return `https://modworkshop.net/mydownloads/previews/${(props.hasThumb && !props.preferHq) ? 'thumbnail_' : ''}${props.src}`;
        } else {
            return `${config.apiUrl}/storage/${props.urlPrefix}/${(props.hasThumb && !props.preferHq) ? 'thumbnail_' : ''}${props.src}`;
        }
    }
});
</script>